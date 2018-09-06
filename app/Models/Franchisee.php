<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franchisee extends Model
{
    public function franchiseeLevel()
    {
        return $this->belongsTo(FranchiseeLevel::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public static function getSeletOptions()
    {
        $options = \DB::table('franchisees')->select('id', 'name as text')->get();
        $selectOption = [];
        foreach ($options as $option) {
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
