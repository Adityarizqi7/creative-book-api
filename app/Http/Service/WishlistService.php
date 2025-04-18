<?php

namespace App\Http\Service;

use App\Http\Models\Book;
use App\Http\Models\User;
use Illuminate\Support\Str;
use App\Http\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class WishlistService
{
    public function toggleWishlist(User $user, Book $book)
    {
        return DB::transaction(function () use ($user, $book) {

            $alreadyWishlisted = DB::table('wishlists')
                ->where('uuid_user', $user->uuid)
                ->where('uuid_book', $book->uuid)
                ->exists();

            $wishlist = DB::table('wishlists')
            ->where('uuid_user', $user->uuid)
            ->where('uuid_book', $book->uuid)->first();

            if ($alreadyWishlisted) {
                DB::table('wishlists')
                ->where('uuid_user', $user->uuid)
                ->where('uuid_book', $book->uuid)
                ->delete();

                return [
                    'message' => 'Dihapus dari wishlist',
                    'status' => 'removed',
                    'wishlist' => $wishlist
                ];
            }

            $uuid = (string) Str::uuid();
            $user->wishlistedBooks()->attach($book->uuid, [
                'uuid' => $uuid,
                'uuid_user' => $user->uuid,
                'uuid_book' => $book->uuid,
            ]);

            $wishlist = Wishlist::where('uuid', $uuid)->first();

            return [
                'message' => 'Ditambahkan ke Wishlist',
                'status' => 'added',
                'wishlist' => $wishlist->only(['uuid','uuid_user','uuid_book','updated_at'])
            ];
        });
    }
}