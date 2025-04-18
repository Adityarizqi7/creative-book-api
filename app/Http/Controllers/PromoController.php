<?php

namespace App\Http\Controllers;

use App\Http\Models\Promo;
use App\Http\Requests\Promo\CreatePromoRequest;
use App\Http\Requests\Promo\UpdatePromoRequest;
use App\Http\Service\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoController extends Controller
{
    protected $promo_service;

    public function __construct(PromoService $promo_service)
    {
        $this->promo_service = $promo_service;
    }

    public function getAllPromos(Promo $promo) {
        try {
            $promos = DB::transaction(function () use ($promo) {
                return $promo->getAllPromos();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Promo berhasil didapatkan.',
                'data' => $promos,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Promo', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengambil data Promo.',
            ]);
        }
    }

    public function createPromo(CreatePromoRequest $request) {
        try {

            $promo = DB::transaction(function () use ($request) {
                $validateData = $request->validated();
                return $this->promo_service->create($validateData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Promo berhasil didapatkan.',
                'data' => $promo->only(['uuid', 'name', 'slug', 'discount', 'start_date_flash_sale', 'end_date_flash_sale', 'updated_at']),
            ], 201);
            
        } catch (\Throwable $e) {
            Log::error('Gagal membuat data Promo', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat data Promo.'
            ]);
        }
    }

    public function updatePromo(UpdatePromoRequest $request, $uuid) {
        try {
            $promo = new Promo();
            $promo_data = $promo->getPromoByUuid($uuid);

            if (!$promo_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Promo tidak ditemukan.'
                ], 404);
            }

            $promo = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validateData = $request->validated();
                return $this->promo_service->update($uuid, $validateData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Promo berhasil diubah.',
                'data' => $promo->only(['uuid', 'name', 'slug', 'discount', 'start_date_flash_sale', 'end_date_flash_sale', 'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Promo', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah data Promo.'
            ]);
        }
    }

    public function deletePromo($uuid) {
        try {

            $promo = new Promo();
            $promo_data = $promo->getPromoByUuid($uuid);

            if (!$promo_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Promo tidak ditemukan.'
                ], 404);
            }
            
            DB::transaction(function () use ($uuid) {
                return $this->promo_service->delete($uuid);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Promo berhasil dihapus.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Promo', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data Promo.'
            ]);
        }
    }
}
