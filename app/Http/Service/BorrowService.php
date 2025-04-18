<?php

namespace App\Http\Service;

use App\Http\Models\Borrow;
use Carbon\Carbon;

class BorrowService
{
    public function borrowBook(array $data)
    {
        return Borrow::create([   
            'uuid_user' => $data['uuid_user'],
            'uuid_book' => $data['uuid_book'],
            'borrowed_at' => $data['borrowed_at'],
            'status' => 'borrowed',
            'fine' => 0,
            'due_at' => now()->addDays(7)->toDateString(),
        ]);
    }

    public function returnedBook($uuid, $fine, $status, array $data) {
        
        $borrow_model = new Borrow();
        $borrow = $borrow_model->getBorrowByUuid($uuid);

        $borrow->update([
            'uuid_user' => $data['uuid_user'],
            'uuid_book' => $data['uuid_book'],
            'returned_at' => $data['returned_at'],
            'fine' => $fine,
            'status' => $status,
        ]);
    
        return $borrow->fresh();
    }
}