<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'message';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['message_id'];

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
  'message_id' => 'int',
  'user_id' => 'int',
  'message_type' => 'int',
  'message_read' => 'int',
  'created_user_id' => 'int',
  'message_created_at' => 'int',
  'message_updated_at' => 'int',
);

}
