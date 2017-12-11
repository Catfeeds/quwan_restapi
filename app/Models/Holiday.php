<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    // '0禁用,1启用',
    const HOLIDAY_STATUS_0 = 0;
    const HOLIDAY_STATUS_1 = 1;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'holiday';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['holiday_id'];

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
        'holiday_id' => 'int',
        'shop_id' => 'int',
        'holiday_start_at' => 'int',
        'holiday_end_at' => 'int',
        'holiday_sort' => 'int',
        'holiday_status' => 'int',
        'holiday_created_at' => 'int',
        'holiday_updated_at' => 'int',
    );

    /**
     * 节日详情页->节日详情
     * @param $holidayId
     * @return array
     */
    public function getInfo($holidayId)
    {

        $data = self::select('*')
//            ->where('holiday_status', '=', self::HOLIDAY_STATUS_1)
            ->where('holiday_id', $holidayId)
            ->first();
        if (true === empty($data)) {
            return [];
        }
        $data = $data->toArray();

        //图片
        $data['img'] = Img::getJoinImgs($data['holiday_id'], Img::IMG_TYPE_B);

        //关联线路
        $routeIds = HolidayJoin::getJoinData($holidayId);
        $data['route'] = Route::getListInfo($routeIds);

        //@todo 关联的订单兑换码
        //$data['code'] = Order::getTypeCode($userId, $joinId, $orderType);

        //@todo 是否可以领红包
        //$data['is_reward'] = 0;

        return $data;
    }


    /**
     * 节日详情页->节日详情
     * @param $holidayId
     * @return array
     */
    public static function getInfoData($holidayId)
    {

        $data = self::select('*')
//            ->where('holiday_status', '=', self::HOLIDAY_STATUS_1)
            ->where('holiday_id', $holidayId)
            ->first();
        if (true === empty($data)) {
            return [];
        }
        $data = $data->toArray();

        //图片
        $data['img'] = Img::getJoinImgs($data['holiday_id'], Img::IMG_TYPE_B);

        //关联线路
        $routeIds = HolidayJoin::getJoinData($holidayId);
        $data['route'] = Route::getListInfo($routeIds);

        //@todo 关联的订单兑换码
        //$data['code'] = Order::getTypeCode($userId, $joinId, $orderType);

        //@todo 是否可以领红包
        //$data['is_reward'] = 0;

        return $data;
    }

}
