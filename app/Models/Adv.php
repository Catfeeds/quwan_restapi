<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adv extends Model
{
    //0禁用,1启用
    const ADV_STATUS_0 = 0;
    const ADV_STATUS_1 = 1;



    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'adv';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['adv_id'];

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
        'adv_id' => 'int',
        'adv_weight' => 'int',
        'adv_content' => 'int',
        'adv_type' => 'int',
        'adv_status' => 'int',
        'adv_created_at' => 'int',
        'adv_updated_at' => 'int',
    ];

}
