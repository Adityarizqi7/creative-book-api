<?php

namespace App\Http\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
    ];

    // protected $casts = [
    //     'updated_at' => 'datetime'
    // ];

    // protected $appends = ['updated_at_formatted'];

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

    // public function getUpdatedAtFormattedAttribute()
    // {
    //     $date = Carbon::parse($this->updated_at)->setTimezone('Asia/Jakarta');
    //     $timezoneLabel = $this->getIndoTimezoneLabel($date->timezoneName);

    //     return $date->translatedFormat('l, d F Y, H:i') . " {$timezoneLabel}";
    // }

    // public function getIndoTimezoneLabel($timezone) {
    //     return match ($timezone) {
    //         'Asia/Jakarta' => 'WIB',
    //         'Asia/Makassar' => 'WITA',
    //         'Asia/Jayapura' => 'WIT',
    //         default => strtoupper($timezone),
    //     };
    // }
 
    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('title')
        ->saveSlugsTo('slug');   
    }

    public function getAllCategories() {
        return self::with(['subcategory:uuid,title,slug,uuid_category'])->get()->whereNull('id_parent_sub_category')->makeHidden(['id', 'created_at']);
    }

    public function getCategoryByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function subcategory() : HasMany {
        return $this->hasMany(SubCategory::class, 'uuid_category','uuid')->whereNull('uuid_parent_sub_category');
    }
}
