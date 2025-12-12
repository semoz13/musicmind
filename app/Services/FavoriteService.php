<?php

namespace App\Services;

use App\Models\User;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class FavoriteService
{
    public function get_favorite_songs()
    {
        $user = Auth::user();
        $favorites = $user->favorites;
        return $favorites;
    }

    public function store($data)
    {
        $user = Auth::user();
        $user_id = $user->userable->id;
        $data['user_id'] = $user_id;
        $favorite = Favorite::create($data);
        return $favorite;
    }

    public function remove_from_favorite(string $id)
    {
        $user = Auth::user();
        $user_id = $user->userable->id;
        $favorite = Favorite::find($id);
        if ($favorite && $favorite->user_id == $user_id){
            $old_favorite = $favorite->delete();
            return $favorite;
        } else {
            throw ValidationException::withMessages([
                'favorite' => ['favorite not found'],
            ]);
        }    

        }

    
}