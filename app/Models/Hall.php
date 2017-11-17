<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'hall';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['hall_id'];

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
  'hall_id' => 'int',
  'hall_score' => 'int',
  'hall_evaluation' => 'int',
  'hall_start_at' => 'int',
  'hall_end_at' => 'int',
  'hall_sort' => 'int',
  'hall_status' => 'int',
  'hall_created_at' => 'int',
  'hall_updated_at' => 'int',
);

}
