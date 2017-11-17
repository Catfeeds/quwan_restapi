<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fav extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'fav';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['fav_id'];

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
  'fav_id' => 'int',
  'join_id' => 'int',
  'fav_type' => 'int',
  'user_id' => 'int',
  'fav_created_at' => 'int',
  'fav_updated_at' => 'int',
);

}
