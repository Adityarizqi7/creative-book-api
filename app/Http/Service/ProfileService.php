<?php  

namespace App\Http\Service;

use App\Http\Models\Profile;
use App\Http\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService {
    public function create(User $user, array $data) {

        return $user->profile()->create([   
            'address' => $data['address'],
            'gender' => $data['gender'],
            'date_birth' => $data['date_birth'],
            'phone' => $data['phone'],
            'avatar' => $data['avatar'],
        ]);
    }

    public function update(User $user, array $data) {

        $user->profile()->update([   
            'address' => $data['address'],
            'gender' => $data['gender'],
            'date_birth' => $data['date_birth'],
            'phone' => $data['phone'],
            'avatar' => $data['avatar'],
        ]);

        return $user->profile->fresh();
    }
}
