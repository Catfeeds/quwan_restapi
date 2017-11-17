<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

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
    protected $casts = array (
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

}
