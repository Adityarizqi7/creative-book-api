<?php

namespace App\Http\Controllers;

use App\Http\Models\Publisher;
use App\Http\Requests\Publisher\CreatePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Http\Service\PublisherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublisherController extends Controller
{
    protected $publisher_service;

    public function __construct(PublisherService $publisher_service)
    {
        $this->publisher_service = $publisher_service;
    }

    public function getAllPublisherBook(Publisher $publisher) {
        try {
            $publishers = DB::transaction(function () use ($publisher) {
                return $publisher->getAllPublishers();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Penerbit berhasil didapatkan.',
                'data' => $publishers,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Penerbit', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Penerbit.'
            ]);
        }   
    }

    public function createPublisher(CreatePublisherRequest $request) {
        try {
            $pubsliher = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                return $this->publisher_service->create($validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Penerbit berhasil ditambahkan.',
                'data' => $pubsliher->only(['uuid', 'name', 'description', 'slug', 'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menambah data Penerbit.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menambah data Penerbit.'
            ], 500);
        }
    }

    public function updatePublisher(UpdatePublisherRequest $request, $uuid) {
        try {

            $publisher = new Publisher();
            $publisher_data = $publisher->getPublisherByUuid($uuid);

            if (!$publisher_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Penerbit tidak ditemukan.'
                ], 404);
            }

            $publisher = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validatedData = $request->validated();
                return $this->publisher_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Penerbit berhasil diubah.',
                'data' => $publisher->only(['uuid', 'name', 'description', 'slug', 'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Penerbit.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Penerbit.'
            ], 500);
        }
    }

    public function deletePublisher($uuid) {
        try {

            $publisher = new Publisher();
            $publisher_data = $publisher->getPublisherByUuid($uuid);

            if (!$publisher_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Penerbit tidak ditemukan.'
                ], 404);
            }
            
            DB::transaction(function () use ($uuid) {
                return $this->publisher_service->delete($uuid);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Penerbit berhasil dihapus.',
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Penerbit.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Penerbit.'
            ], 500);
        }
    }
}
