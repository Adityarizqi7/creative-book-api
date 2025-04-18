<?php

namespace App\Http\Controllers;

use App\Http\Models\Writer;
use App\Http\Requests\Writer\CreateWriterRequest;
use App\Http\Requests\Writer\UpdateWriterRequest;
use App\Http\Service\WriterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WriterController extends Controller
{
    protected $writer_service;

    public function __construct(WriterService $writer_service)
    {
        $this->writer_service = $writer_service;   
    }

    public function getAllWritersBook(Writer $writer) {
        try {
            $writers = DB::transaction(function () use ($writer) {
                return $writer->getAllWriters();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Penulis Buku berhasil didapatkan.',
                'data' => $writers,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Penulis Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Penulis Buku.'
            ]);
        }
    }

    public function createWriter(CreateWriterRequest $request) {
        try {
            $writer = Db::transaction(function () use ($request) {
                $validatedData = $request->validated();
                return $this->writer_service->create($validatedData); 
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Penulis berhasil didapatkan.',
                'data' => $writer->only(['uuid', 'name', 'label_gender', 'slug', 'updated_at']),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal menambah data Penulis Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambah data Penulis Buku.'
            ]);
        }
    }

    public function updateWriter(UpdateWriterRequest $request, $uuid) {
        try {

            $writer = new Writer();
            $writer_data = $writer->getWriterByUuid($uuid);

            if (!$writer_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Penulis Buku tidak ditemukan.'
                ], 404);
            }

            $writer = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validatedData = $request->validated();
                return $this->writer_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Penulis berhasil diubah.',
                'data' => $writer->only(['uuid', 'name', 'slug', 'updated_at', 'label_gender']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Penulis Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah data Penulis Buku.' 
            ]);
        }
    }

    public function deleteWriter($uuid) {
        try {

            $writer = new Writer();
            $writer_data = $writer->getWriterByUuid($uuid);

            if (!$writer_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Penulis Buku tidak ditemukan.'
                ], 404);
            }

            DB::transaction(function () use ($uuid) {
                return $this->writer_service->delete($uuid);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Penulis berhasil dihapus.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Penulis Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah data Penulis Buku.'
            ]);
        }
    }
}
