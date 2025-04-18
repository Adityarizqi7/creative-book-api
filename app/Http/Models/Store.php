<?php

namespace App\Http\Models;

use App\Http\Models\City;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;

class Store extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::creating(function ($profile) {
            $lastid = self::max('id');
            $profile->id = $lastid ? $lastid + 1 : 1;
        });
    }

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('name')
        ->saveSlugsTo('slug');   
    }

    public function getAllStores() {
        return self::all()->makeHidden(['id', 'created_at']);
    }

    public function getStoreByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_store', 'uuid_store', 'uuid_book')
                    ->withPivot('original_price', 'final_price', 'uuid_promo')
                    ->withTimestamps();
    }

}
