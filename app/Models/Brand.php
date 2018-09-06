<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public static function getSeletOptions()
    {
        $options = \DB::table('brands')->select('id', 'name as text')->get();
        $selectOption = [];
        foreach ($options as $option) {
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
