<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteDay extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'route_day';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['route_day_id'];

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
  'route_day_id' => 'int',
  'route_id' => 'int',
  'route_day_sort' => 'int',
);

    //获取线路下所有行程
    public static function getDayData($routeId)
    {
        $day = RouteDay::select('route_day_id','route_day_intro','route_day_sort')
            ->where('route_id','=',$routeId)
            ->orderBy('route_day_sort')
            ->get()
            ->toArray();
        return $day;
    }

}
