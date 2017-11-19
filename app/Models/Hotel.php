<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    //'0禁用,1启用',
    const HOTEL_STATUS_0 = 0;
    const HOTEL_STATUS_1 = 1;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'hotel';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['hotel_id'];

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
        'hotel_id' => 'int',
        'hotel_start_at' => 'int',
        'hotel_end_at' => 'int',
        'hotel_sort' => 'int',
        'hotel_status' => 'int',
        'hotel_created_at' => 'int',
        'hotel_updated_at' => 'int',
    );

    /**
     * 目的->评价最好的酒店
     * @param $hotelIds
     * @return array
     */
    public function getMudiList($hotelIds)
    {

        $data = self::select('hotel_id', 'hotel_name', 'hotel_score', 'hotel_evaluation')
            ->where('hotel_sort', '=', self::HOTEL_STATUS_1)
            ->whereIn('hotel_id', $hotelIds)
            ->orderBy('hotel_score_num', 'desc')
            ->limit(2)
            ->get()
            ->toArray();
        if (true === empty($data)) {
            return [];
        }

        foreach ($data as $keyR => &$valueR) {
            //图片
            $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['hotel_id']);

        }

        return $data;
    }

}
