<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Img extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'img';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['img_id'];

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
  'img_id' => 'int',
  'join_id' => 'int',
  'img_sort' => 'int',
  'img_type' => 'int',
  'img_status' => 'int',
  'img_created_at' => 'int',
  'img_updated_at' => 'int',
);

}
