<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attractions extends Model
{

    //是否可退货,0否,1是
    const ATTRACTIONS_IS_REFUND_0 = 0;
    const ATTRACTIONS_IS_REFUND_1 = 1;
    //0禁用,1启用
    const ATTRACTIONS_STATUS_0 = 0;
    const ATTRACTIONS_STATUS_1 = 1;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'attractions';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['attractions_id'];

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
        'attractions_id' => 'int',
        'shop_id' => 'int',
        'attractions_is_refund' => 'int',
        'attractions_start_at' => 'int',
        'attractions_end_at' => 'int',
        'attractions_sort' => 'int',
        'attractions_status' => 'int',
        'attractions_created_at' => 'int',
        'attractions_updated_at' => 'int',
    );


    /**
     * 获取目的地下热门景点
     * @param $attractionsId
     * @return array
     */
    public function getMudiLists($attractionsId)
    {
        $data = self::select('attractions_id', 'attractions_name', 'attractions_intro', 'attractions_price', 'attractions_score', 'attractions_evaluation', 'attractions_lon', 'attractions_lat', 'attractions_suggest')->where('attractions_status', '=', self::ATTRACTIONS_STATUS_1)
            ->whereIn('attractions_id', $attractionsId)
            ->orderBy('attractions_sales_num', 'desc')
            ->limit(2)
            ->get()
            ->toArray();
        if (true === empty($data)) {
            return [];
        }

        foreach ($data as $keyA => &$valueA) {
            //图片
            $valueA['img'] = Img::getOneImg($valueA['attractions_id'], Img::IMG_TYPE_1);
            //分类
            $valueA['cid'] = CidMap::getCidsInfo($valueA['attractions_id'], CidMap::CID_MAP_TYPE_1);

        }

        return $data;
    }

    /**
     * 获取景点详情
     * @param $attractionsId
     * @return array
     */
    public function getInfo($attractionsId)
    {
        $data = self::select('attractions_id', 'attractions_name', 'attractions_intro', 'attractions_price', 'attractions_score', 'attractions_evaluation', 'attractions_lon', 'attractions_lat', 'attractions_suggest')->where('attractions_status', '=', self::ATTRACTIONS_STATUS_1)
            ->where('attractions_id','=', $attractionsId)
            ->first();
        if (true === empty($data)) {
            return [];
        }
        $data = $data->toArray();

        //图片
        $data['img'] = Img::getJoinImgs($data['attractions_id'], Img::IMG_TYPE_1);
        //分类
        $data['cid'] = CidMap::getCidsInfo($data['attractions_id'], CidMap::CID_MAP_TYPE_1);

        //@todo 关联的订单兑换码
        //$data['code'] = Order::getTypeCode($userId, $joinId, $orderType);

        //@todo 是否可以领红包
        //$data['is_reward'] = 0;

        return $data;
    }
}
