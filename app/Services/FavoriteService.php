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

    public function removeFromFavorite(string $spotify_id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $favorite = Favorite::where('user_id', $user_id)
            ->where('spotify_id', $spotify_id)
            ->first();
        if (! $favorite) {
            throw ValidationException::withMessages([
                'favorite' => ['Favorite song not found.'],
            ]);
        }
        $favorite->delete();

        return $favorite;
    }

    public function get_favorite_songs()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $favorites = Favorite::where('user_id', $user_id)->get();

        return $favorites;
    }

    public function filterFavoritesByMood(string $mood)
    {
        $user = Auth::user();

        $favorites = Favorite::where('user_id', $user->id)->get();

        if ($favorites->isEmpty()){
            return [];
        }

        $spotifyIds = $favorites->pluck('spotify_id')->toArray();

        $tracks = app(SongService::class)->getSongsByIds($spotifyIds);
        
        
        if(empty($tracks)) {
            return [];
        }
        
        $audioFeatures = app(SpotifyService::class)
            ->getAudioFeaturesByIds($spotifyIds);
            
        return collect($tracks['tracks'] ?? [])
            ->filter(function ($track) use ($audioFeatures, $mood){
                $features = $audioFeatures[$track['id']] ?? null;
                if(!$features){
                    return false;
                }
                return $this->matchMood($features, $mood);
            })

            ->values()
            ->all();
            logger()->debug('Mood match result', [
                'track_id' => $track['id'],
                'mood' => $mood,
                'matched' => $matched,
                'energy' => $features['energy'] ?? null,
                'valence' => $features['valence'] ?? null,
            ]);


      
    }
    private function matchMood(array $f, string $mood): bool
    {
        return match (strtolower($mood)) {
            'chill' => $f['energy'] < 0.4 && $f['acousticness'] > 0.5,
            'happy' => $f['valence'] > 0.6 && $f['energy'] > 0.5,
            'sad' => $f['valence'] < 0.4 && $f['energy'] < 0.5,
            'energetic' => $f['energy'] > 0.7 && $f['tempo'] > 120,
            'party' => $f['danceability'] > 0.7 && $f['energy'] > 0.7,
            'focus' => $f['instrumentalness'] > 0.5 && $f['speechiness'] < 0.2,

            default => false,
        };
    }
}
