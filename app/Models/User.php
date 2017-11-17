<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['user_id'];

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
  'user_id' => 'int',
  'user_sex' => 'int',
  'user_is_binding' => 'int',
  'user_msg_num' => 'int',
  'user_status' => 'int',
  'user_created_at' => 'int',
  'user_updated_at' => 'int',
);

}
