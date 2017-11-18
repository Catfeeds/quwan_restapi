<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationJoin extends Model
{
    //1景点,2路线,3酒店,4餐厅
    const DESTINATION_JOIN_TYPE_1 = 1;
    const DESTINATION_JOIN_TYPE_2 = 2;
    const DESTINATION_JOIN_TYPE_3 = 3;
    const DESTINATION_JOIN_TYPE_4 = 4;

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

//获取线路下第一张图片
    public static function getOneJoinImg($destinationId)
    {

        $data = self::select('join_id', 'destination_join_type')
            ->where('destination_id', '=', $destinationId)
            ->orderBy('destination_join_sort')
            ->first();
        if (!$data) {
            return '';
        }

        //获取第一张图片
        $img = Img::getOneImg($data->join_id, $data->destination_join_type);

        return $img;

    }

}
