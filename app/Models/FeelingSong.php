<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FeelingSong extends Pivot
{

    
    public function feeling()
    {
        return $this->belongsTo(Feeling::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
