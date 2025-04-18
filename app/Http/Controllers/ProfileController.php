<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Service\ProfileService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;

class ProfileController extends Controller
{
    protected $profile_service;

    public function __construct(ProfileService $profile_service)
    {
        $this->profile_service = $profile_service;
    }

    public function getProfile() {
        try {
            $profile = DB::transaction(function () {
                $userHasProfile = auth()->guard()->user();
                return $userHasProfile->profile;
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Profil Pengguna berhasil didapatkan.',
                'data' => $profile->only(['uuid', 'address', 'label_gender', 'phone', 'avatar', 'uuid_user', 'updated_at', 'birth_date_formatted']),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Profil Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengambil data Profil Pengguna.' 
            ], 500);
        }
    }

    public function createProfile(CreateProfileRequest $request) {

        try {

            $userHasProfile = auth()->guard()->user();

            if ($userHasProfile->profile) {
                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Pengguna sudah memiliki profile.'
                ], 500);
            }

            $profile = DB::transaction(function () use ($request, $userHasProfile) {

                $validatedData = $request->validated();

                if($request->has('avatar')) {

                    $avatarData = $request->input('avatar');
                    $image = $this->decodeBase64Image($avatarData);

                    // Menghasilkan nama file untuk gambar baru
                    $today = date("Ymd");
                    $random_digit = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                    $avatar_name = "avatars-{$today}{$random_digit}.{$this->getImageExtension($avatarData)}";

                    // Menyimpan gambar base64 ke disk
                    $avatar_path = Storage::disk('public')->put('images/avatars/' . $avatar_name, $image);

                    // Jika berhasil menyimpan, simpan path gambar di database
                    if ($avatar_path) {
                        $validatedData['avatar'] = 'images/avatars/' . $avatar_name;
                    }
                }

                return $this->profile_service->create(auth()->guard()->user(), $validatedData);
            }); 

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Profil Pengguna berhasil diubah.',
                'data' => $profile->only(['uuid', 'address', 'gender', 'date_birth', 'phone', 'avatar', 'uuid_user', 'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Profil Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Profil Pengguna.'
            ], 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request) {

        try {

            $profile = DB::transaction(function () use ($request) {

                $validatedData = $request->validated();

                $avatar_exists = auth()->guard()->user()->profile;

                if (!$avatar_exists) {
                    return response()->json([
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'Profil tidak ditemukan.'
                    ], 404);
                }

                if($request->has('avatar')) {

                    $avatarData = $request->input('avatar');
                    $image = $this->decodeBase64Image($avatarData);

                    // Cek apakah avatar sudah ada, jika ada maka hapus gambar lama
                    if ($avatar_exists->avatar) {
                        Storage::disk('public')->delete($avatar_exists->avatar);
                    }

                    // Menghasilkan nama file untuk gambar baru
                    $today = date("Ymd");
                    $random_digit = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                    $avatar_name = "avatars-{$today}{$random_digit}.{$this->getImageExtension($avatarData)}";

                    // Menyimpan gambar base64 ke disk
                    $avatar_path = Storage::disk('public')->put('images/avatars/' . $avatar_name, $image);

                    // Jika berhasil menyimpan, simpan path gambar di database
                    if ($avatar_path) {
                        $validatedData['avatar'] = 'images/avatars/' . $avatar_name;
                    }
                }

                return $this->profile_service->update(auth()->guard()->user(), $validatedData);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Profil berhasil diubah.',
                'data' => $profile->only(['uuid', 'address', 'gender', 'date_birth', 'phone', 'avatar', 'uuid_user', 'updated_at']),
            ], 200);

        } catch (\Throwable $e) {
            
            Log::error('Gagal mengubah data Profile Pengguna.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Profile Pengguna.'
            ], 500);
        }
    }

    private function decodeBase64Image($base64String) {
        $image = str_replace('data:image/png;base64,', '', $base64String);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace('data:image/jpg;base64,', '', $image);
        return base64_decode($image);
    }

    private function getImageExtension($base64String) {
        if (strpos($base64String, 'data:image/jpeg;base64,') === 0) {
            return 'jpg';
        } elseif (strpos($base64String, 'data:image/png;base64,') === 0) {
            return 'png';
        } elseif (strpos($base64String, 'data:image/gif;base64,') === 0) {
            return 'gif';
        }
        return 'png';
    }
}
