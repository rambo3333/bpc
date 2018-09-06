<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseeLevel extends Model
{
    public static function getSeletOptions()
    {
        $options = \DB::table('franchisee_levels')->select('id', 'star as text')->get();
        $selectOption = [];
        foreach ($options as $option) {
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
