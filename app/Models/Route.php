<?php

namespace App\Models;

use App\Repository\RouteDayRepository;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    //0禁用,1启用',
    const ROUTE_STATUS_0 = 0;
    const ROUTE_STATUS_1 = 1;

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
    protected $casts = array(
        'route_id' => 'int',
        'route_day_num' => 'int',
        'user_id' => 'int',
        'route_status' => 'int',
        'route_created_at' => 'int',
        'route_updated_at' => 'int',
    );

    /**
     * 目的->销售最好的线路
     * @param $routeIds
     * @return array
     */
    public function getMudiList($routeIds)
    {
        $data = self::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro')
            ->where('route_status', '=', self::ROUTE_STATUS_1)
            ->whereIn('route_id', $routeIds)
            ->orderBy('route_use_num', 'desc')
            ->limit(2)
            ->get()
            ->toArray();
        if (true === empty($data)) {
            return [];
        }

        foreach ($data as $keyR => &$valueR) {


            $valueR['route_intro'] = htmlspecialchars_decode($valueR['route_intro']);

            //图片
            $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['route_id']);
            //分类
            $valueR['cid'] = CidMap::getCidsInfo($valueR['route_id'], CidMap::CID_MAP_TYPE_3);

        }

        return $data;
    }


    /**
     * 获取列表简介数据
     * @param $routeIds
     * @return array
     */
    public static function getListInfo($routeIds)
    {
        $data = self::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro')
            ->where('route_status', '=', self::ROUTE_STATUS_1)
            ->whereIn('route_id', $routeIds)
            ->orderBy('route_use_num', 'desc')
            ->get()
            ->toArray();
        if (true === empty($data)) {
            return [];
        }

        foreach ($data as $keyR => &$valueR) {

            $valueR['route_intro'] = htmlspecialchars_decode($valueR['route_intro']);

            //图片
            $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['route_id']);
            //分类
            $valueR['cid'] = CidMap::getCidsInfo($valueR['route_id'], CidMap::CID_MAP_TYPE_3);

        }

        return $data;
    }


    /**
     * 获取线路详情数据
     * @param $routeId
     * @return array
     */
    public static function getInfo($routeId)
    {
        $data = self::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro')
            ->where('route_status', '=', self::ROUTE_STATUS_1)
            ->where('route_id', '=', $routeId)
            ->first();
        if (true === empty($data)) {
            return [];
        }
        $data = $data->toArray();


        $data['route_intro'] = htmlspecialchars_decode($data['route_intro']);

        //分类
        $data['cid'] = CidMap::getCidsInfo($data['route_id'], CidMap::CID_MAP_TYPE_3);

        return $data;
    }


    /**
     * 获取用户列表简介数据
     * @param $data
     * @return array
     */
    public static function getUserListInfo($data)
    {

        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;

        $query = self::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro');
        $query->where('route_status', '=', self::ROUTE_STATUS_1);
        $query->where('user_id', '=', $data['user_id']);
        $query->orderBy('route_id', 'desc');


        $result['_count'] = $query->count();
        $result['data'] = $query->skip($offset)->take($limit)->get()->toArray();

        if (true === empty($result['data'])) {
            return [];
        }

        foreach ($result['data'] as $keyR => &$valueR) {


            $valueR['route_intro'] = htmlspecialchars_decode($valueR['route_intro']);

            //图片
            $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['route_id']);
            //分类
            $valueR['cid'] = CidMap::getCidsInfo($valueR['route_id'], CidMap::CID_MAP_TYPE_3);

        }

        return $result;
    }

}
