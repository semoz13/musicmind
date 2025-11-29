<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;
  // You don't need to add both guarded and fillable because they serve opposite purposes.
  // whatever you dont want to be mass assigned put it in guarded

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'spotify_id',
        'image_url'
    ];
//this relation is for the songs that belong to this artist
    public function songs() 
    {
        return $this->hasMany(Song::class);
    }
}
