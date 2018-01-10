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
     * 酒店详情页->酒店详情
     * @param $hotelId
     * @return array
     */
    public static function getInfo($hotelId)
    {

        $data = self::select('*')
            ->where('hotel_status', '=', self::HOTEL_STATUS_1)
            ->where('hotel_id', $hotelId)
            ->orderBy('hotel_score_num', 'desc')
            ->first();
        if (true === empty($data)) {
            return [];
        }
        $data = $data->toArray();


        $data['hotel_intro'] = htmlspecialchars_decode($data['hotel_intro']);

        $data['hotel_intro'] = lose_space(strip_tags($data['hotel_intro']));

        //图片
        $data['img'] = Img::getJoinImgs($data['hotel_id'], Img::IMG_TYPE_C);


        return $data;
    }

    /**
     * 目的->评价最好的酒店
     * @param $hotelIds
     * @return array
     */
    public function getMudiList($hotelIds)
    {

        $data = self::select('hotel_id', 'hotel_name', 'hotel_score', 'hotel_evaluation')
            ->where('hotel_status', '=', self::HOTEL_STATUS_1)
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
            $valueR['img'] = Img::getOneImg($valueR['hotel_id'], Img::IMG_TYPE_C);

        }

        return $data;
    }

}
