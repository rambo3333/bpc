<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = ['agent_id', 'franchisee_id', 'password', 'name', 'mobile', 'worker_no',
                            'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image', 'bank', 'parent_id',
                            'user_id'];

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
