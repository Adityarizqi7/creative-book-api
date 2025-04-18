<?php

namespace App\Http\Models;

use App\Http\Models\Book;
use App\Http\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'uuid_book', 'uuid');
    }
}
