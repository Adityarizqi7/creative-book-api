<?php

namespace App\Http\Models;

use App\Http\Models\Book;
use App\Http\Models\Store;
use App\Http\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'uuid_store', 'uuid');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'uuid_book', 'uuid');
    }
}
