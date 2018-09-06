<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
