<?php

namespace App\Http\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
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

        static::creating(function ($promo) {
            $lastid = self::max('id');
            $promo->id = $lastid ? $lastid + 1 : 1;
        });
    }

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('name')
        ->saveSlugsTo('slug');   
    }

    public function getAllPromos() {
        return self::all()->makeHidden(['id', 'created_at', 'uuid_store']);
    }

    public function getPromoByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'book_stores', 'uuid_promo', 'uuid_store')
            ->withPivot('uuid_book', 'original_price', 'final_price')
            ->withTimestamps();
    }

}
