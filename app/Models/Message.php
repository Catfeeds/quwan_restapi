<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    //'1全部用户,2单个用户',
    const MESSAGE_TYPE_1 = 1;
    const MESSAGE_TYPE_2 = 2;

    //'0未读1已读',
    const MESSAGE_READ_0 = 0;
    const MESSAGE_READ_1 = 1;
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

    public static function getInfo($messageId)
    {
        $data = self::select('message_id', 'message_read','message_title', 'message_comment','message_type')->where('message_id','=',$messageId)->first();
        if (!$data) {
            return [];
        }
        return $data->toArray();
    }
}
