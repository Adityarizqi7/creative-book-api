<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'uuid_sub_category',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::creating(function ($user) {
            $lastId = self::max('id');
            $user->id = $lastId ? $lastId + 1 : 1;
        });
    }
 
    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('title')
        ->saveSlugsTo('slug')
        ->doNotGenerateSlugsOnUpdate()
        ;   
    }

    public function getAllCategories() {
        return Category::with(['subCategory:uuid,title,slug'])->get()->makeHidden('uuid_sub_category');
    }

    public function getCategoryByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function SubCategory() : HasMany {
        return $this->hasMany(SubCategory::class, 'uuid_category');
    }
}
