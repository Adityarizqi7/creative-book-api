<?php  

namespace App\Http\Service;

use App\Http\Models\User;
use App\Http\Models\UserHasRoles;
use Illuminate\Support\Facades\Auth;

class UserService {

    public function getAuthUser() {
        $userHasRole = UserHasRoles::with([
            'user' => function ($query) {
                $query->select('uuid', 'name', 'email', 'refresh_token')
                      ->with('profile:address,gender,date_birth,phone,avatar');
            },
            'role:uuid,name'
        ])->whereHas('user', function ($q) {
            $q->where('uuid', 'like', Auth::user()->uuid);
        })->get();
        
        return $userHasRole->map(function ($uhr) {
            return [
                'uuid' => $uhr->user->uuid ?? null,
                'name' => $uhr->user->name ?? null,
                'mail' => $uhr->user->email ?? null,
                'refresh_token' => $uhr->user->refresh_token ?? null,
                'address' => $uhr->user->profile->address ?? null,
                'gender' => $uhr->user->profile->gender ?? null,
                'date_birth' => $uhr->user->profile->date_birth ?? null,
                'phone' => $uhr->user->profile->phone ?? null,
                'avatar' => $uhr->user->profile->avatar ?? null,
                'roles' => [
                    $uhr->role->name
                ]
            ];
        });
    }   

    public function update(User $user, array $data) {

        $user->update([   
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'password' => $data['new_password'] ?? $user->password,
        ]);

        return $user->fresh();
    }

    public function delete(User $user){
        $user->delete();
    }
}
