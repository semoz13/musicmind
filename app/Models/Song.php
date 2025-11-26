<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

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
}
