<?php

namespace App\Http\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $guarded = [];

    protected function casts() : array {
        return [
            'date_birth' => 'date'
        ];
    }

    protected $appends = ['birth_date_formatted', 'label_gender'];

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

    public function getBirthDateFormattedAttribute() {
        $date = Carbon::parse($this->date_birth)->setTimezone('Asia/Jakarta');
        return $date->translatedFormat('l, d F Y');
    }

    public function getLabelGenderAttribute() {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getAuthProfile($uuid) {
        self::where('uuid', $uuid)->first();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'uuid_user', 'uuid');
    }
}
