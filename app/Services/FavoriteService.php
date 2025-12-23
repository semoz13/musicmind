<?php

namespace App\Services;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FavoriteService
{
    public function addToFavorite(array $data)
    {
        // $data should contain 'spotify_id'
        // check if 'spotify_id' is provided
        if (! isset($data['spotify_id'])) {
            throw ValidationException::withMessages([
                'spotify_id' => ['The spotify_id field is required.'],
            ]);
        }
        $user = Auth::user();
        $user_id = $user->id;
        $data['user_id'] = $user_id;

        // Check if the favorite already exists for this user
        $existing_favorite = Favorite::where('user_id', $user_id)
            ->where('spotify_id', $data['spotify_id'])
            ->first();

        if ($existing_favorite) {
            throw ValidationException::withMessages([
                'favorite' => ['This song is already in your favorites.'],
            ]);
        }

        $favorite = Favorite::create($data);

        return $favorite;
    }

    public function removeFromFavorite(int $user_id,string $spotifySongId): bool
    {
        $user = Auth::user();
        $user_id = $user->id;
        $favorite = Favorite::where('spotify_id', $id)
            ->where('user_id', $user_id)
            ->first();
        if ($favorite && $favorite->user_id == $user_id) {
            $old_favorite = $favorite->delete();

            return $old_favorite;
        } else {
            throw ValidationException::withMessages([
                'favorite' => ['favorite not found'],
            ]);
        }

    }

    public function get_favorite_songs()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $favorites = Favorite::where('user_id', $user_id)->get();

        return $favorites;
    }
}
