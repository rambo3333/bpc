<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class);
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }

    public static function getSeletOptions()
    {
        $options = \DB::table('workers')->select(\DB::raw("id, concat(name, '-', mobile) as text"))->get();
        $selectOption = [];
        foreach ($options as $option) {
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
