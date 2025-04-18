<?php

namespace App\Http\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_roles', 'uuid_role', 'uuid_user');
    }

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
}
