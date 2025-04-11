<?php

namespace App\Http\Controllers\api\auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\service\AuthenticationService;
use App\Http\Requests\User\UserRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Authentication\LoginRequest;


class AuthController extends Controller
{
    protected $authenticaton_service;

    public function __construct(AuthenticationService $authenticaton_service)
    {
        $this->authenticaton_service = $authenticaton_service;
    }

    public function register(UserRequest $user_request) {

        try {
            $user = DB::transaction( function () use ($user_request) {
                $validatedData = $user_request->validated();
                return $this->authenticaton_service->register($validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Pendaftaran Pengguna Berhasil.',
                'data' => $user->only(['uuid', 'email', 'name']),
            ], 201);

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

                
                if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                    throw ValidationException::withMessages([
                        'email' => 'Email atau Password Salah'
                    ]);
                }
                
                $token = $user->createToken($validatedData['email']);

                return compact('user', 'token');
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Proses Masuk Berhasil',
                'data' => [
                    'user' => $result['user']->only(['uuid', 'email', 'name']),
                    'token' => $result['token']
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Proses Masuk Gagal', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Proses Masuk Gagal ' . $e->getMessage(),
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
            Log::error('Gagal keluar aplikasi', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal keluar aplikasi'
            ], 500);
        }
    }
}
