<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayJoin extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'holiday_join';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['holiday_join_id'];

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
  'holiday_join_id' => 'int',
  'holiday_id' => 'int',
  'route_id' => 'int',
);
    //获取所有关联线路数据
    public static function getJoinData($holidayId)
    {
        $data = self::where('holiday_id','=',$holidayId)->pluck('route_id');
        return $data;
    }

}
