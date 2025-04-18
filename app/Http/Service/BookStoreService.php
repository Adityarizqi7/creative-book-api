<?php

namespace App\Http\Service;

use App\Http\Models\Book;

class BookStoreService
{
    public function attachBookToStore(array $data)
    {
        $book = Book::where('uuid', $data['uuid_book'])->first();
        $book->stores()->attach($data['uuid_store'], [
            'original_price' => $data['original_price'],
            'final_price' => $data['final_price'],
            'uuid_promo' => $data['uuid_promo'] ?? null,
        ]);

        return $book->load('stores');

    }
}