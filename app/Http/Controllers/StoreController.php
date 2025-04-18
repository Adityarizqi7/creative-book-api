<?php

namespace App\Http\Controllers;

use App\Http\Models\Store;
use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Service\StoreService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    protected $store_service;

    public function __construct(StoreService $store_service)
    {
       $this->store_service = $store_service; 
    }

    public function getAllStoresBook(Store $store) {
        try {
            $stores = DB::transaction(function () use ($store) {
                return $store->getAllStores();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Toko berhasil didapatkan.',
                'data' => $stores,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Toko Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Toko Buku. ' . $e->getMessage()
            ]);
        }
    }

    public function createStore(CreateStoreRequest $request) {
        try {
            $store = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                return $this->store_service->create($validatedData);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Toko berhasil ditambahkan.',
                'data' => $store->only(['uuid', 'name', 'city_name', 'address', 'slug', 'updated_at']),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan data Toko Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menambahkan data Toko Buku.' 
            ]);
        }
    }

    public function updateStore(UpdateStoreRequest $request, $uuid) {
        try {

            $store_model = new Store();
            $store = $store_model->getStoreByUuid($uuid);

            if (!$store) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Toko Buku tidak ditemukan.'
                ], 404);
            }

            $store = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validatedData = $request->validated();
                return $this->store_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Toko berhasil di diubah.',
                'data' => $store->only(['uuid', 'name', 'city_name', 'address', 'slug',  'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Toko Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Toko Buku.'
            ], 500);
        }
    }

    public function deleteStore($uuid) {
        try {

            $store_model = new Store();
            $store = $store_model->getStoreByUuid($uuid);

            if (!$store) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Toko Buku tidak ditemukan.'
                ], 404);
            }
            
            DB::transaction(function () use ($uuid) {
                return $this->store_service->delete($uuid);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Toko berhasil di dihapus.',
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Toko Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Toko Buku.'
            ], 500);
        }
    }
}
