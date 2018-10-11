<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    protected $fillable = ['contract', 'yf', 'refund_status', 'refund_no'];

    const STATUS_NOT_PAID = 'not_paid'; //待支付
    const STATUS_PAID = 'paid'; //支付成功
    const STATUS_PAID_FAIL = 'paid_fail'; //支付失败
    const STATUS_REFUND = 'refund'; //退款成功

    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    public static $statusMap = [
        self::STATUS_NOT_PAID => '未支付',
        self::STATUS_PAID => '支付成功',
        self::STATUS_PAID_FAIL => '支付失败',
        self::STATUS_REFUND => '退款成功',
    ];

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static function getAvailableRefundNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            // 为了避免重复我们在生成之后在数据库中查询看看是否已经存在相同的退款订单号
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_no', 'worker_no');
    }
}
