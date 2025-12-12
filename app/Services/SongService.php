<?php

namespace App\Services;

use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SongService
{
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
