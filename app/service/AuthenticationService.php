<?php  

namespace App\service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationService {

    public function register(array $data) {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
