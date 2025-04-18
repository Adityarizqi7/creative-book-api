<?php

namespace App\Http\Controllers;

use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Service\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $user_service;

    public function __construct(UserService $user_service)
    {   
        $this->user_service = $user_service;
    }

    public function getAuthUser() {
        try {

            $user = DB::transaction(function () {
                return $this->user_service->getAuthUser();
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Data Akun Pengguna berhasil didapatkan.',
                'data' => $user,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mendapatkan data Akun Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mendapatkan data Akun Pengguna.'
            ], 500);
        }
        return $this->user_service->getAuthUser();
    }

    public function updateUser(User $user, Request $request) {
        try {

            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'sometimes'
                ],
                'email' => [
                    'email',
                    'sometimes',
                    'required',
                ],
                'new_password' => [
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                    'nullable',
                ],
                'password' => [
                    'required_with:new_password',
                    'current_password:' . config('auth.defaults.guard')
                ]
            ], [
                'name.required' => 'Nama wajib diisi.',
                
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            
                'new_password.required' => 'Password baru wajib diisi.',
                'new_password.regex' => 'Password baru harus memiliki minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan simbol.',
            
                'password.required' => 'Password lama wajib diisi.',
                'password.current_password' => 'Password lama tidak cocok.'
            ]);
            
            $user = DB::transaction(function () use ($validatedData) {
                return $this->user_service->update(auth()->guard()->user(), $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Akun Pengguna berhasil diubah.',
                'data' => $user->only('uuid', 'name', 'email', 'email_verified_at', 'deleted_at', 'updated_at')
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Akun Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Akun Pengguna.'
            ], 500);
        }
    }

    public function deleteUser() {
        try {

            DB::transaction(function (){
                return $this->user_service->delete(auth()->guard()->user());
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Profil Pengguna berhasil dihapus.',
            ], 201);
            
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Pengguna.'
            ], 500);
        }
    }
}
