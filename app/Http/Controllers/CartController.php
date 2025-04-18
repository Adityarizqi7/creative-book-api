<?php

namespace App\Http\Controllers;

use App\Http\Models\Book;
use App\Http\Models\Cart;
use App\Http\Service\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;

class CartController extends Controller
{
    protected $cart_service;

    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
    }


    public function addToCart(CreateCartRequest $request, Book $book)
    {
        try {
            $cart = DB::transaction(function () use ($request, $book) {
                $validatedData = $request->validated();
                $user = auth()->guard()->user();

                $existing = Cart::where('uuid_user', $user->uuid)
                    ->where('uuid_book', $book->uuid)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $validatedData['quantity']);
                    return $existing->refresh();
                } else {
                    return $this->cart_service->create($validatedData);
                }
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Buku ditambahkan ke Keranjang.',
                'data' => $cart->only(['uuid','uuid_user','uuid_book','quantity','updated_at']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal memasukkan Buku ke Keranjang.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal memasukkan Buku ke Keranjang.'
            ], 500);
        }
    }

    public function updateCartQuantity(UpdateCartRequest $request, $uuid)
    {
        try {
            $bookAtCart = Cart::where('uuid_user', auth()->guard()->user()->uuid)->where('uuid_book', $uuid)->first();

            if (!$bookAtCart) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Buku tidak ada di Keranjang.',
                ], 400); 
            }

            $cart = DB::transaction(function () use ($request, $uuid) {
                $validatedData = $request->validated();
                return $this->cart_service->updateQuantity($uuid, $validatedData['quantity']);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Jumlah buku di keranjang berhasil diperbarui.',
                'data' => $cart->only(['uuid','uuid_user','uuid_book','quantity','updated_at']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal update keranjang', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal memperbarui keranjang.',
            ], 500);
        }
    }
    public function removeFromCart($uuid) {
        try {

            $user = auth()->guard()->user();
            $bookAtCart = Cart::where('uuid_user', $user->uuid)->where('uuid_book', $uuid)->first();

            // dd($uuid);

            if (!$bookAtCart) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Buku tidak ada di Keranjang.',
                ], 404); 
            }

            $cart = DB::transaction(function () use ($user, $uuid) {
                return $this->cart_service->removeFromCart($user, $uuid);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Buku dihapus dari Keranjang.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus Buku dari Keranjang', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus Buku dari Keranjang.',
            ], 500);
        }
    }
}
