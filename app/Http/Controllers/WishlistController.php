<?php

namespace App\Http\Controllers;

use App\Http\Models\Book;
use Illuminate\Support\Facades\Log;
use App\Http\Service\WishlistService;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function getAllWishlistsBook()
    {
        $user = auth()->guard()->user();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Wishlist berhasil didapatkan.',
            'data' => $user->wishlistedBooks()->get()
        ], 200);
    }

    public function toggleWishlist($uuid)
    {
        $book = new Book();
        $book_exists = $book->where('uuid', $uuid)->first();

        $user = Auth::guard()->user();

        try {
            $result = $this->wishlistService->toggleWishlist($user, $book_exists);

            return response()->json([
                'code' => 200,
                'status' => $result['status'],
                'message' => $result['message'],
                'data' => $result
            ], 200);
            

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah wishlist. ' . $e->getMessage(),
            ]);
        }
    }
}
