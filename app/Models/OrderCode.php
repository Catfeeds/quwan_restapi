<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCode extends Model
{
    //'是否兑换核销,0未,1已',
    const IS_EXCHANGE_0 = 0;
    const IS_EXCHANGE_1 = 1;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_code';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['order_code_id'];

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = array(
        'order_code_id' => 'int',
        'order_id' => 'int',
        'is_exchange' => 'int',
        'exchange_user_id' => 'int',
        'exchange_at' => 'int',
        'created_at' => 'int',
    );

    //获取订单兑换码
    public static function getOrderCode($orderId)
    {
        $data = self::where('order_id', '=', $orderId)
            ->pluck('code')
            ->toArray();
        return $data;
    }

}
