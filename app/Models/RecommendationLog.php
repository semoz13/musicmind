<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    use HasFactory;

    // You don't need to add both guarded and fillable because they serve opposite purposes.
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'user_id',
        'input_type',
        'input_value',
        'recommended_song_id',
        
    ];
    //this relation is for the user who made the recommendation
    public function users()
    {
        $this->belongsTo(User::class);

    }
    //this relation is for the song that was recommended
    public function songs()
    {
        $this->belongsTo(Song::class);
        
    }
}
