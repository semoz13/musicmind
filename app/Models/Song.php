<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'spotify_id',
        'title',
        'artist_id',
        'genre_id',
        'overview',
        'mood',
        'release_date',
        'preview_url',
        'image_url'
    ];    

    public function artists()
    {
        $this->belongsTo(Artist::class);
    }

    public function recommendation_logs()
    {
        $this->hasMany(RecommendationLog::class);
    }

    public function favorites()
    {
        $this->hasMany(Favorite::class);
    }

    public function feelings()
    {
        $this->belongsToMany(Feeling::class);
    }

    public function genres()
    {
        $this->belongsToMany(Genre::class);
    }

    public function favoriteByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_song');
    }
    public function audioFeature()
    {
        return $this->hasOne(AudioFeature::class);
    }
}
