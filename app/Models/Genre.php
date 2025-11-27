<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'genre_name'
    ];

    public function songs()
    {
        $this->belongsToMany(Song::class);
    }
}
