<?php

namespace App\Http\Controllers;

use App\Http\Models\Province;
use App\Regency;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProvinceController extends Controller
{
    public function getSyncProvinces() {
        if (request('secret') !== env('SYNC_SECRET')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        } 
        Artisan::call('sync:provinces');
    }

    public function getProvinces(Province $province) {
        try {
            $provinces = DB::transaction(function () use ($province) {
                return $province->getAllProvinces();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Provinsi berhasil didapatkan.',
                'data' => $provinces,
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Provinsi', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Provinsi.'
            ]);
        }
    }

    public function getRegenciesByProvince($provinceId) {
        try {
            $response = Http::get("https://open-api.my.id/api/wilayah/regencies/{$provinceId}");

            if ($response->successful()) {
                return response()->json($response->json());
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Regensi berhasil didapatkan.',
                    'data' => $response->json(),
                ], 200);
            }

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengambil data Regensi.'
            ], 500);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Regensi', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Regensi.'
            ]);
        }
    }
}
