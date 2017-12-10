<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //'1登录，2分享，3首页浏览',
    const LOG_TYPE_1 = 1;
    const LOG_TYPE_2 = 2;
    const LOG_TYPE_3 = 3;

    //'1景点,2目的地，3路线,4节日，5酒店,6餐厅，7图片',
    const LOG_JOIN_TYPE_1 = 1;
    const LOG_JOIN_TYPE_2 = 2;
    const LOG_JOIN_TYPE_3 = 3;
    const LOG_JOIN_TYPE_4 = 4;
    const LOG_JOIN_TYPE_5 = 5;
    const LOG_JOIN_TYPE_6 = 6;
    const LOG_JOIN_TYPE_7 = 7;


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'log';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['log_id'];

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
    protected $casts = [
        'log_id' => 'int',
        'log_type' => 'int',
        'log_time' => 'int',
        'user_id' => 'int',
        'log_join_type' => 'int',
        'join_id' => 'int',

    ];

}
