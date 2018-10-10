<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['contract', 'yf', ''];

    const STATUS_NOT_PAID = 'not_paid'; //待支付
    const STATUS_PAID = 'paid'; //支付成功
    const STATUS_PAID_FAIL = 'paid_fail'; //支付失败
    const STATUS_REFUND = 'refund'; //退款成功

    public static $statusMap = [
        self::STATUS_NOT_PAID => '未支付',
        self::STATUS_PAID => '支付成功',
        self::STATUS_PAID_FAIL => '支付失败',
        self::STATUS_REFUND => '退款成功',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_no', 'worker_no');
    }
}
