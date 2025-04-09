<?php  

namespace App\service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationService {

    public function register(array $data) {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
