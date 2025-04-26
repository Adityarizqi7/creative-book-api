<?php

namespace App\Services;

use App\Http\Models\BookStore;
use App\Http\Models\Promo;

class OrderService
{
    public static function calculateGrossAmount(string $uuidBook, string $uuidStore, int $quantity = 1): int
    {
        $bookStore = BookStore::where('uuid_book', $uuidBook)
            ->where('uuid_store', $uuidStore)
            ->first();

        if (!$bookStore) {
            abort(404, 'Data book & store tidak ditemukan.');
        }

        $finalPrice = $bookStore->final_price;

        if ($bookStore->uuid_promo) {
            $promo = Promo::where('uuid', $bookStore->uuid_promo)->first();
            if ($promo && $promo->discount && now()->between($promo->start_date_flash_sale, $promo->end_date_flash_sale)) {
                $finalPrice = $finalPrice - ($finalPrice * ($promo->discount / 100));
            }
        }

        return (int) round($finalPrice * $quantity);
    }
}
