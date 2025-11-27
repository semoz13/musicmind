<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeling extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = [
        'mood_name',
        'description'
    ];
    public function songs()
    {
        $this->belongsToMany(Song::class);
    }
}
