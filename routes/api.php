<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Category Book
    Route::get('/book/categories', [CategoryController::class, 'getAllCategoriesBook']);
    Route::post('/book/category', [CategoryController::class, 'createCategory']);
    Route::put('/book/category/{uuid}', [CategoryController::class, 'updateCategory']);
    Route::delete('/book/category/{uuid}', [CategoryController::class, 'deleteCategory']);
});