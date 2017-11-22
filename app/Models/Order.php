<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    //1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片
    const ORDER_TYPE_A = 1;
    const ORDER_TYPE_B = 4;
    const ORDER_TYPE_C = 5;
    const ORDER_TYPE_D = 6;

    // '是否评价,0未,1已',
    const ORDER_IS_SCORE_0 = 0;
    const ORDER_IS_SCORE_1 = 1;

    //`` tinyint(1) NOT NULL DEFAULT '10' COMMENT '订单状态(10未付款,20已支付，30已核销，40已评价，0未付款取消',
    const ORDER_STATUS_0 = 0;
    const ORDER_STATUS_10 = 10;
    const ORDER_STATUS_20 = 20;
    const ORDER_STATUS_30 = 30;
    const ORDER_STATUS_40 = 40;

    //'取消方式(1未付款手动取消,2未付款自动取消,3已付款手动取消[退款])',
    const ORDER_CANCEL_TYPE_1 = 1;
    const ORDER_CANCEL_TYPE_2 = 2;
    const ORDER_CANCEL_TYPE_3 = 3;


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['order_id'];

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
        'order_id' => 'int',
        'shop_id' => 'int',
        'join_id' => 'int',
        'order_type' => 'int',
        'order_num' => 'int',
        'order_is_exchange' => 'int',
        'order_is_score' => 'int',
        'order_status' => 'int',
        'user_id' => 'int',
        'order_pay_at' => 'int',
        'order_refund_at' => 'int',
        'order_cancel_at' => 'int',
        'order_verify_at' => 'int',
        'order_created_at' => 'int',
        'order_updated_at' => 'int',
    );

    //获取所有未兑换的兑换码
    public static function getTypeCode($userId, $joinId, $orderType)
    {
        $data = self::select('c.code')
            ->leftJoin('order_code as c', 'c.order_id', '=', 'order.order_id')
            ->where('order.user_id', '=', $userId)
            ->where('order.join_id', '=', $joinId)
            ->where('order.order_type', '=', $orderType)
            ->where('c.is_exchange', '=', OrderCode::IS_EXCHANGE_0)
            ->get()
            ->toArray();
        return $data;
    }
}
