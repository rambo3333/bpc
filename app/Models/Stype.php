<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stype extends Model
{
    public static function getSeletOptions()
    {
        $options = \DB::table('stypes')->select('id', 'name as text')->get();
        $selectOption = [];
        foreach ($options as $option) {
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
