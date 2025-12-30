<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioFeature extends Model
{
    protected $table = 'audio_features';
    
    protected $fillable = [
        'spotify_id',
        'danceability',
        'energy',
        'valence',
        'acousticness',
        'instrumentalness',
        'speechiness',
        'tempo'
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}

