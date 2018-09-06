<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }
}
