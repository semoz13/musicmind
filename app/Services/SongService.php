<?php

namespace App\Services;

use App\Models\Song;
use App\Models\User;
use Illuminate\support\facades\Http;
use Illuminate\support\facades\Cache;
use Illuminate\Support\Facades\Auth;

class SongService extends SpotifyService
{
    protected string $baseUrl = 'https://api.spotify.com/v1';
    
    
    protected function client()
    {
        return Http::withOptions([
            'verify' => false,
        ])->withToken($this->getAccessToken());

    }
    
    public function getSongsByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $response = $this->client()->get($this->baseUrl. '/tracks', [
            'ids' => implode(',' , $ids),
        ]);

        return $response->json('tracks') ?? [];


        if (!$response->successful()){
            return [];
        }
    }

    public function searchSongs(string $query, int $limit = 10): array
    {
        $response = $this->client()->get(
            $this->baseUrl . '/search', [
            'q' => $query,
            'type' => 'track',
            'limit' => $limit,
        ]);

        if (!$response->successful()){
            return [];
        }
        return $response->json('tracks.items') ?? [];
    }
    
    
        

    public function apply_filters(array $data)
    {
        $query = Song::query();
        
        if (!empty($data['genre_id'])) {
            $query->whereHas('genres', function( $q ) use ($data) {
                $q->where('genre_song.genre_id', $data['genre_id']);
            });
        }

        if (!empty($data['artist_id'])) {
                $query->whereHas('artists', function ($q) use ($data){
                    $q->where('artist_song.artist_id',$data['artist_id']);
                });
        }

        if (!empty($data['search'])) {
            $query->where(function ($query) use ($data){
                $query->where('title','like','%'.$data['search'].'%')
                ->orWhere('overview','like','%'.$data['search'].'%')
                ->orWhereJsonContains('keywords', $data['search']);
            }); 


    }
    $songs = $query->paginate(10);
    return $songs;    
}

}
