<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'route';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['route_id'];

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
  'route_id' => 'int',
  'route_day_num' => 'int',
  'user_id' => 'int',
  'route_status' => 'int',
  'route_created_at' => 'int',
  'route_updated_at' => 'int',
);

}
