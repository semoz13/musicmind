<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Register a new customer.
     *
     * @param array $data
     * @return \App\Models\Customer
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(array $data)
    {
        $user  = User::create($data);
        $token = $user->createToken('user')->plainTextToken;
        return $info = ['user' => $user, 'token' => $token];
    }

    public function login(array $data)
    {
        if (!Auth::attempt($data)) {
            return['error'=> 'invalid email or password'];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return[
            'user'=>$user,
            'token'=>$token
        ];
    }

    public function logout()
    {
    $user = Auth::user();

    if ($user && $user->currentAccessToken()) {
        $user->currentAccessToken()->delete();
        return true;
    }

    return false;
    }
}
