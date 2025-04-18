<?php

namespace App\Http\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasSlug;
    
    protected $fillable = [
        'title',
        'slug',
        'uuid_category',
        'uuid_parent_sub_category',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::creating(function ($sub_category) {
            $lastId = self::max('id');
            $sub_category->id = $lastId ? $lastId + 1 : 1;
        });
    }

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
        ->generateSlugsFrom('title')
        ->saveSlugsTo('slug')
        ->doNotGenerateSlugsOnUpdate()
        ;   
    }

    public function getAllSubCategories() {
        return self::with(['category:uuid,title,slug', 'parent', 'children'])->get()->makeHidden(['uuid_category', 'uuid_parent_sub_category', 'id', 'created_at']);
    }

    public function getSubCategoryByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    public function category() :BelongsTo {
        return $this->belongsTo(Category::class, 'uuid_category', 'uuid');
    }

    public function parent() :BelongsTo {
        return $this->belongsTo(SubCategory::class, 'uuid_parent_category', 'uuid');
    }

    public function children() :HasMany {
        return $this->hasMany(SubCategory::class, 'uuid_parent_sub_category', 'uuid');
    }
}
