<?php

namespace App\Http\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Models\Cart;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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

    public function getUserByUuid($uuid) {
        return self::where('uuid', $uuid)->first();
    }
    
    public function getUserByEmail($email) {
        $user = $this->where('email', $email)->first();
        return $user;
    }
    
    public function checkEmailIsExists($email, $uuid) {
        return $this->where('email', $email)->where('uuid', '!=', $uuid)->exists();
    }

    public function profile() :HasOne {
        return $this->hasOne(Profile::class, 'uuid_user', 'uuid');
    }

    public function wishlistedBooks()
    {
        return $this->belongsToMany(Book::class, 'wishlists', 'uuid_user', 'uuid_book')
                    ->withPivot(['uuid'])
                    ->withTimestamps();
    }

    public function cartItems(): HasMany {
        return $this->hasMany(Cart::class, 'uuid_user', 'uuid');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'uuid_user', 'uuid_role')->withPivot(['uuid']);
    }
}
