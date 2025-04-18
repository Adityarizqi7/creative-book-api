<?php

namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Service\SubCategoryService;
use App\Http\Requests\SubCategory\UpdateSubCategoryRequest;
use App\Http\Requests\SubCategory\CreateSubCategoryRequest;

class SubCategoryController extends Controller
{
    protected $sub_category_service;

    public function __construct(SubCategoryService $sub_category_service)
    {
       $this->sub_category_service = $sub_category_service; 
    }

    public function getAllSubCategoriesBook(SubCategory $sub_category) {
        try {
            $categories = DB::transaction(function () use ($sub_category) {
                return $sub_category->getAllSubCategories();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Sub Kategori berhasil didapatkan.',
                'data' => $categories,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Sub Kategori Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Sub Kategori Buku.'
            ]);
        }
    }

    public function createSubCategory(CreateSubCategoryRequest $request) {
        try {

            $categoryExists = Category::where('uuid', $request->uuid_category)->exists();

            if (! $categoryExists) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan.',
                ], 404);
            }

            $category = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                return $this->sub_category_service->create($validatedData);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Sub Kategori berhasil ditambahkan.',
                'data' => $category->only(['uuid', 'title', 'slug', 'uuid_category', 'uuid_parent_sub_category', 'updated_at']),
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan data Sub Kategori Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data Sub Kategori Buku.'
            ]);
        }
    }

    public function updateSubCategory(UpdateSubCategoryRequest $request, $uuid) {
        try {

            $sub_category = new SubCategory();
            $subCategory = $sub_category->getSubCategoryByUuid($uuid);

            if (!$subCategory) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Sub Kategori Buku tidak ditemukan.'
                ], 404);
            }

            $categoryExists = Category::where('uuid', $request->uuid_category)->exists();

            if (! $categoryExists) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan.',
                ], 404);
            }

            $category = DB::transaction(function () use ($request) {
                $uuid = $request->route('uuid');
                $validatedData = $request->validated();
                return $this->sub_category_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Sub Kategori berhasil di diubah.',
                'data' => $category->only(['uuid', 'title', 'slug', 'uuid_category', 'uuid_parent_sub_category', 'updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal mengubah data Sub Kategori Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Sub Kategori Buku.'
            ], 500);
        }
    }

    public function deleteSubCategory($uuid) {
        try {

            $sub_category = new SubCategory();
            $subCategory = $sub_category->getSubCategoryByUuid($uuid);

            if (!$subCategory) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Sub Kategori Buku tidak ditemukan.'
                ], 404);
            }
            
            DB::transaction(function () use ($uuid) {
                return $this->sub_category_service->delete($uuid);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Sub Kategori berhasil di dihapus.',
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Sub Kategori Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Sub Kategori Buku.'
            ], 500);
        }
    }
}
