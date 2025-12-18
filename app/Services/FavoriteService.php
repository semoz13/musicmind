<?php

namespace App\Services;

use App\Models\User;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class FavoriteService
{
    public function getUserFavoriteIds(int $userid): array
    {
        return Favorite::where('user_id',$userid)
        ->pluck('spotify_song_id')
        ->toArray();
    }

    public function addToFavorites(int $user_id,string $spotifySongId): Favorite
    {
        return Favorite::firstorCreate([
            'user_id' => $user_id,
            'spotify_song_id' => $spotifySongId
        ]);
    }

    public function removeFromFavorite(int $user_id,string $spotifySongId): bool
    {
        return Favorite::where('user_id' , $user_id)
        ->where('spotify_song_id' , $spotifySongId)
        ->delete() > 0;    
    }

    
}