<?php

namespace App\Http\Controllers;

use App\Http\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Service\AuthenticationService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Authentication\LoginRequest;

class AuthController extends Controller
{
    protected $authenticaton_service;

    public function __construct(AuthenticationService $authenticaton_service)
    {
        $this->authenticaton_service = $authenticaton_service;
    }

    public function register(Request $request) {

        try {

            $validatedData = $request->validate([
                'name' => 'string|required',
                'email' => [
                    'email',
                    'required',
                    Rule::unique('users')
                ],
                'password' => [
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                    'required',
                ],
                    'roles' => 'required|array',
                    'roles.*' => 'exists:roles,uuid',
            ], [
                
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            
                'password.required' => 'Password baru wajib diisi.',
                'password.regex' => 'Password harus memiliki minimal 8 karakter, 1 huruf besar, 1 angka, dan 1 simbol.',

                'roles.required' => 'Roles harus diisi.',
                'roles.array' => 'Roles harus berupa array.',
                'roles.*.exists' => 'Role yang dipilih tidak valid.',
            ]);

            $user = DB::transaction( function () use ($validatedData) {
                $user_roles = $this->authenticaton_service->register($validatedData);
                if (!empty($validatedData['roles']) && is_array($validatedData['roles'])) {
                    $user_roles->roles()->attach($validatedData['roles'], [
                        'uuid' => Str::uuid(),
                        'uuid_user' => $user_roles->uuid
                    ]);
                }
                return $user_roles;
            });
            
            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Pendaftaran Pengguna Berhasil.',
                'data' => [
                    $user->only(['uuid', 'email', 'name']),
                ]
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Pendaftaran Pengguna Gagal', ['exception' => $e->getMessage()]);
            
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Pendaftaran Pengguna Gagal. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $login_request, User $user_model) {
        try {
            $result = DB::transaction(function () use ($login_request, $user_model) {
                $validatedData = $login_request->validated();
        
                $user = $user_model->getUserByEmail($validatedData['email']);

                if (!Auth::attempt($validatedData)) {
                    return response()->json(['message' => 'Login gagal'], 401);
                }                

                if (!$user) {
                    throw ValidationException::withMessages([
                        'message' => 'Email atau Password Salah'
                    ]);
                }
                
                if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                    throw ValidationException::withMessages([
                        'message' => 'Email atau Password Salah'
                    ]);
                }
                
                $token = $user->createToken('access_token');

                $token->accessToken->forceFill([
                    'expires_at' => now()->addHours(24),
                ])->save();       
                
                $accessToken = $token->plainTextToken;

                // Refresh Token
                $refreshToken = Str::random(64);
                $user->refresh_token = $refreshToken;
                $user->refresh_token_expires_at = now()->addDays(7);
                $user->save();

                return compact('user', 'accessToken', 'refreshToken');
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Proses Masuk Berhasil',
                'data' => [
                    'user' => $result['user']->only(['uuid', 'email', 'name']),
                    'access_token' => $result['accessToken'],
                    'refresh_token' => $result['refreshToken'],
                    'expires_in' => 86400, // 24 jam
                    'refresh_token_expires_in' => 604800 // 7 hari
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Proses Masuk Gagal.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Proses Masuk Gagal.',
            ], 401);
        }
    }

    public function logout(Request $request) {
        try {
            $result = DB::transaction(function () use ($request) {
                $request->user()->currentAccessToken()->delete();
                return $request->user();
            });

            return response()->json([
                'message' => 'Berhasil keluar dari aplikasi',
                'data' => $result
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal keluar aplikasi.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal keluar aplikasi.'
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $refreshToken = $request->input('refresh_token');

            $user = User::where('refresh_token', $refreshToken)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Refresh token tidak valid.'
                ], 401);
            }

            if (Carbon::parse($user->refresh_token_expires_at)->isPast()) {
                return response()->json([
                    'message' => 'Refresh token sudah kadaluarsa.'
                ], 401);
            }

            $token = DB::transaction(function () use ($user) {

                $user->tokens()->delete();
                $accessToken = $user->createToken('access_token')->plainTextToken;

                $user->currentAccessToken()->forceFill([
                    'expires_at' => now()->addHours(24)
                ])->save();

                $newRefreshToken = Str::random(64);
                $user->refresh_token = $newRefreshToken;
                $user->refresh_token_expires_at = now()->addDays(7);
                $user->save();

                return compact('accessToken', 'newRefreshToken');
            });

            return response()->json([
                'access_token' => $token['accessToken'],
                'token_type' => 'Bearer',
                'expires_in' => 86400, // 24 
                'refresh_token' => $token['newRefreshToken'],
                'refresh_token_expires_in' => 604800 // 7 hari
            ]);
        } catch (\Exception $e) {
            Log::error('Error refreshing token.');

            return response()->json([
                'message' => 'Gagal memperbarui token.',
            ], 500);
        }
    }

}
