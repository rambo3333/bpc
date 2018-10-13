<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = ['agent_id', 'franchisee_id', 'password', 'name', 'mobile', 'worker_no',
                            'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image', 'bank', 'parent_id',
                            'user_id', 'is_effect', 'level', 'star', 'manage_level', 'sale_total_num', 'sale_num',
                            'upgraded_at'];

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

    //获取当前级别星星最大数量
    public function getStarMax($level)
    {
        switch ($level) {
            case 1:
                return config('car.level1_star_max');
            case 2:
                return config('car.level2_star_max');
            case 3:
                return config('car.level3_star_max');
            case 4:
                return config('car.level4_star_max');
        }
    }

    //获取当前个代级别
    public function getLevelName($level)
    {
        switch ($level) {
            case 1:
                return config('car.level1_name');
            case 2:
                return config('car.level2_name');
            case 3:
                return config('car.level3_name');
            case 4:
                return config('car.level4_name');
        }
    }

    //获取当前级别提成数据
    public function getCommissionRate($level)
    {
        switch ($level) {
            case 1:
                return config('car.level1_percent');
            case 2:
                return config('car.level2_percent');
            case 3:
                return config('car.level3_percent');
            case 4:
                return config('car.level4_percent');
        }
    }

    //获取下一级别销售目标数量
    public function getNextLevelSaleGoalNum($level)
    {
        $level += 1;
        switch ($level) {
            case 1:
                return config('car.level1_sale_goal');
            case 2:
                return config('car.level2_sale_goal');
            case 3:
                return config('car.level3_sale_goal');
            case 4:
                return config('car.level4_sale_goal');
        }
    }
}
