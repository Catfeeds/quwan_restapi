<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteDayJoin extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'route_day_join';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['route_day_join_id'];

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
  'route_day_join_id' => 'int',
  'route_day_id' => 'int',
  'join_id' => 'int',
  'route_day_join_sort' => 'int',
  'route_day_join_type' => 'int',
);

}
