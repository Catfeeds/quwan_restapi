<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'score';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['score_id'];

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
  'score_id' => 'int',
  'user_id' => 'int',
  'join_id' => 'int',
  'order_id' => 'int',
  'score_type' => 'int',
  'score_status' => 'int',
  'score_created_at' => 'int',
  'score_updated_at' => 'int',
);

}
