<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Writer extends Model
{
    protected $guarded = [];

    public function getLabelGenderAttribute() {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
