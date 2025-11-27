<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetectMood extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
     

    protected $fillable = [
        'user_id',
        'detected_mood' , 
        'source'
    ];

    public function users() 
    {
        return $this->belongsTo(User::class);
    }


}
