<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\service\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Http\Client\Request;

class CategoryController extends Controller
{

    protected $category_service;

    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
    }

    public function getAllCategoriesBook(Category $category) {
        try {
            $categories = DB::transaction(function () use ($category) {
                return $category->getAllCategories();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Kategori berhasil didapatkan.',
                'data' => $categories,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Kategori Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Kategori Buku'
            ]);
        }
    }

    public function createCategory(CreateCategoryRequest $request) {

        try {
            $category = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                return $this->category_service->create($validatedData);
            }); 

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Kategori berhasil di ditambahkan.',
                'data' => $category->only(['uuid', 'title', 'slug', 'created_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menambah data Kategori Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menambah data Kategori Buku'
            ], 500);
        }
    }

    public function updateCategory(UpdateCategoryRequest $request) {
        try {
            $category = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validatedData = $request->validated();
                return $this->category_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Kategori berhasil di diubah.',
                'data' => $category->only(['uuid', 'title', 'slug', 'created_at']),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Kategori Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Kategori Buku'
            ], 500);
        }
    }

    public function deleteCategory($uuid) {
        try {
            DB::transaction(function () use ($uuid) {
                return $this->category_service->delete($uuid);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Kategori berhasil di dihapus.',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Kategori Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Kategori Buku ' . $e->getMessage()
            ], 500);
        }
    }
}
