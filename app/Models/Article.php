<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{



    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'article';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;


}
