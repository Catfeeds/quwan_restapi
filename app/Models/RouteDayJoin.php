<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteDayJoin extends Model
{

    //1景点,2目的地，3路线,4节日，5酒店,6餐厅
    const ROUTE_DAY_JOIN_TYPE_A = 1;
    const ROUTE_DAY_JOIN_TYPE_B = 2;
    const ROUTE_DAY_JOIN_TYPE_C = 4;
    const ROUTE_DAY_JOIN_TYPE_D = 5;
    const ROUTE_DAY_JOIN_TYPE_E = 6;


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
    protected $casts = array(
        'route_day_join_id' => 'int',
        'route_day_id' => 'int',
        'join_id' => 'int',
        'route_day_join_sort' => 'int',
        'route_day_join_type' => 'int',
    );

    //获取线路下第一张图片
    public static function getOneJoinImg($routeId)
    {

        $data = self::select('join_id', 'route_day_join_type')
            ->where('route_id', '=', $routeId)
            ->orderBy('route_day_join_sort')
            ->first();
        if (!$data) {
            return '';
        }

        //获取第一张图片
        $img = Img::getOneImg($data->join_id, $data->route_day_join_type);

        return $img;

    }

}
