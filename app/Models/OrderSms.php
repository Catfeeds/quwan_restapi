<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSms extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_sms';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['order_sms_id'];

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
        'order_sms_id' => 'int',
        'order_id' => 'int',
        'created_at' => 'int',
    );

}
