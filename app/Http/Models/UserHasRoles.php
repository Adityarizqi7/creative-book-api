<?php

namespace App\Http\Models;

use App\Http\Models\Role;
use App\Http\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHasRoles extends Model
{
    protected $table = 'user_has_roles';

    protected $fillable = ['uuid', 'uuid_user', 'uuid_role'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'uuid_role', 'uuid');
    }
}
