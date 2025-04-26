<?php

namespace App\Http\Models;


use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    use HasSlug;

    protected $casts = [
        'images' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::creating(function ($writer) {
            $lastid = self::max('id');
            $writer->id = $lastid ? $lastid + 1 : 1;
        });
    }

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('name')
        ->saveSlugsTo('slug');   
    }

    public function getAllBooks() {
        return self::all()->makeHidden(['id', 'created_at']);
    }

    public function getBookByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'book_stores', 'uuid_book', 'uuid_store', 'uuid')
                    ->withPivot('original_price', 'final_price', 'uuid_promo')
                    ->withTimestamps();
    }

}
