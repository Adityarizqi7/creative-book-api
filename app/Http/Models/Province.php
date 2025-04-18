<?php

namespace App\Http\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $guarded = [];

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

    public function getAllProvinces() {
        return self::all();
    }
}
