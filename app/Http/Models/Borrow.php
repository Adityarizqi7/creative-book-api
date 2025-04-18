<?php

namespace App\Http\Models;

use App\Http\Models\Book;
use App\Http\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $guarded = [];

    protected $appends = ['formatted_fine'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::creating(function ($category) {
            $lastId = self::max('id');
            $category->id = $lastId ? $lastId + 1 : 1;
        });
    }


    public function getFormattedFineAttribute() {
        return 'Rp. ' . number_format($this->fine, 0, ',', '.');
    }

    public function getAllBorrows() {
        return self::with(['book:uuid,name,slug,image,date_publish,variant_code,variant_name'])->get()->makeHidden(['id', 'created_at']);
    }

    public function getAllLateBooks() {
        return self::with(['book:uuid,name,slug,image,date_publish,variant_code,variant_name'])->where('status', 'late')->get()->makeHidden(['id', 'created_at']);
    
    }
    public function getAllReturnedooks() {
        return self::with(['book:uuid,name,slug,image,date_publish,variant_code,variant_name'])->where('status', 'returned')->get()->makeHidden(['id', 'created_at']);
    }

    public function getBorrowByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function getBookWasBorrowed($uuid) {
        return self::where('uuid_book', $uuid)->where('status', 'borrowed')->where('due_at', '>=', now()->toDateString())->exists();
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
