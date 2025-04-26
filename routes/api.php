<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WriterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\CheckTokenExpiry;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Provinces & Regencies
Route::get('/sync-provinces', [ProvinceController::class, 'getSyncProvinces']);
Route::get('/provinces', [ProvinceController::class, 'getProvinces']);
Route::get('/regencies/{id}', [ProvinceController::class, 'getRegenciesByProvince']);

Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::get('/user/roles', [UserController::class, 'getAllRoles']);

Route::middleware(['auth:sanctum'])->group(function () {

    /* User */
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User 
    Route::get('/user/logged-in', [UserController::class, 'getAuthUser']);
    Route::put('/user', [UserController::class, 'updateUser']);
    Route::delete('/user', [UserController::class, 'deleteUser']);
    
    // Profile
    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
    Route::post('/user/profile', [ProfileController::class, 'createProfile']);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'getAllWishlistsBook']);
    Route::post('/wishlist/toggle/{uuid}', [WishlistController::class, 'toggleWishlist']);

    // Cart
    Route::post('/cart/add/{uuid}', [CartController::class, 'addToCart']);
    Route::put('/cart/{uuid}', [CartController::class, 'updateCartQuantity']);
    Route::delete('/cart/{uuid}', [CartController::class, 'removeFromCart']);

    // Borrow
    Route::get('/book/borrows', [BorrowController::class, 'getAllBorrowsBook']);
    Route::post('/book/borrow/{uuid}', [BorrowController::class, 'borrowBook']);
    Route::put('/book/returned/{uuid}', [BorrowController::class, 'returnedBook']);
    Route::get('/book/borrow/late-books', [BorrowController::class, 'getAllLateBooks']);
    Route::get('/book/borrow/returned-books', [BorrowController::class, 'getAllReturnedBooks']);
    
    // Route untuk membuat pesanan
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::post('/pay', [TransactionController::class, 'pay']);
    // Route untuk memperbarui pesanan dengan Midtrans token
    Route::post('/orders/{orderId}/update', [OrderController::class, 'updateOrderWithMidtrans']);
    // Route untuk memperbarui status pesanan
    Route::post('/orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);

    /* Admin */
    // Book
    Route::get('/books', [BookController::class, 'getAllBooks']);
    Route::post('/book', [BookController::class, 'createBook']);
    Route::put('/book/{uuid}', [BookController::class, 'updateBook']);
    Route::delete('/book/{uuid}', [BookController::class, 'deleteBook']);

    // Category Book
    Route::get('/book/categories', [CategoryController::class, 'getAllCategoriesBook']);
    Route::post('/book/category', [CategoryController::class, 'createCategory']);
    Route::put('/book/category/{uuid}', [CategoryController::class, 'updateCategory']);
    Route::delete('/book/category/{uuid}', [CategoryController::class, 'deleteCategory']);
    
    // Sub Category Book
    Route::get('/book/sub-categories', [SubCategoryController::class, 'getAllSubCategoriesBook']);
    Route::post('/book/sub-category', [SubCategoryController::class, 'createSubCategory']);
    Route::put('/book/sub-category/{uuid}', [SubCategoryController::class, 'updateSubCategory']);
    Route::delete('/book/sub-category/{uuid}', [SubCategoryController::class, 'deleteSubCategory']);

    // Writer
    Route::get('/book/writers', [WriterController::class, 'getAllWritersBook']);
    Route::post('/book/writer', [WriterController::class, 'createWriter']);
    Route::put('/book/writer/{uuid}', [WriterController::class, 'updateWriter']);
    Route::delete('/book/writer/{uuid}', [WriterController::class, 'deleteWriter']);

    // Promo
    Route::get('/promos', [PromoController::class, 'getAllPromos']);
    Route::post('/promo', [PromoController::class, 'createPromo']);
    Route::put('/promo/{uuid}', [PromoController::class, 'updatePromo']);
    Route::delete('/promo/{uuid}', [PromoController::class, 'deletePromo']);

    // Publisher
    Route::get('/publishers', [PublisherController::class, 'getAllPublisherBook']);
    Route::post('/publisher', [PublisherController::class, 'createPublisher']);
    Route::put('/publisher/{uuid}', [PublisherController::class, 'updatePublisher']);
    Route::delete('/publisher/{uuid}', [PublisherController::class, 'deletePublisher']);

    // Store
    Route::get('/stores', [StoreController::class, 'getAllStoresBook']);
    Route::post('/store', [StoreController::class, 'createStore']);
    Route::put('/store/{uuid}', [StoreController::class, 'updateStore']);
    Route::delete('/store/{uuid}', [StoreController::class, 'deleteStore']);
    
});