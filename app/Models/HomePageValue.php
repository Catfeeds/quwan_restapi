<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageValue extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'home_page_value';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['home_page_value_id'];

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
        'home_page_value_id' => 'int',
        'home_page_id' => 'int',
        'value_id' => 'int',
        'sort' => 'int',
    ];



}
