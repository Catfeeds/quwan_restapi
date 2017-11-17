<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationJoin extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'destination_join';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['destination_join_id'];

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
  'destination_join_id' => 'int',
  'destination_id' => 'int',
  'join_id' => 'int',
  'destination_join_sort' => 'int',
  'destination_join_type' => 'int',
);

}
