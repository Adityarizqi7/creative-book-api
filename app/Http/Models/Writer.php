<?php

namespace App\Http\Models;

use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class Writer extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected $appends = ['label_gender'];

    public function getLabelGenderAttribute() {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }

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

    public function getAllWriters() {
        return self::all()->makeHidden(['id', 'created_at']);
    }

    public function getWriterByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }

    // public function book(): HasMany {
    //     return $this->hasMany('uuid_writer', 'uuid');
    // }
}
