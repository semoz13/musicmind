<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
    public function updateProfile(array $data)
    {
        /** @var \App\Models\User $user */
       
        $user = Auth::user();
       
        if (isset($data['avatar'])){
            if($user->avatar_url){
                Storage::disk('public')->delete($user->avatar_url);
            }
            $path = $data['avatar']->store('avatars', 'public');
            $user->avatar_url = $path;

        }
        if (isset($data['name'])){
            $user->name = $data['name'];
        }
        
        $user->save();

        return $user;
    }
    public function changePassword(array $data)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return ['error' => 'Current password is incorrect'];
        }

        $user->password = $data['password'];
        $user->save();

        // revoke all tokens (optional but recommended)
        $user->tokens()->delete();

            return true;
    }
}
