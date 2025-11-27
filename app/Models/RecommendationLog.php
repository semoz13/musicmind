<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'user_id',
        'input_type',
        'input_value',
        'recommended_song_id',
        
    ];
    public function users()
    {
        $this->belongsTo(User::class);

    }

    public function songs()
    {
        $this->belongsTo(Song::class);
        
    }
}
