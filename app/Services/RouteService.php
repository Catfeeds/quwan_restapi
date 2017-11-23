<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Route;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Img;
use App\Models\RouteDay;
use App\Models\RouteDayJoin;
use App\Repository\RouteDayRepository;

class RouteService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $route;
    protected $cidMap;
    protected $hall;
    protected $routeDayRepository;
    protected $routeDayJoin;
    protected $routeDay;

    public function __construct(
        RouteDay $routeDay,
        RouteDayJoin $routeDayJoin,
        RouteDayRepository $routeDayRepository,
        Hall $hall,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Route $route
    )
    {


        $this->routeDay = $routeDay;
        $this->routeDayJoin = $routeDayJoin;
        $this->routeDayRepository = $routeDayRepository;
        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;

    }

    //复制线路到用户
    public function useRoute($routeId, $userId)
    {


        //检测线路是否存在
        $res = $this->route->where('route_id', '=', $routeId)->first();
        if (!$res) {
            throw new UnprocessableEntityHttpException(850004);
        }

        $res = $res->toArray();

        //@todo 检测是否以及使用过该线路

        //@todo 注意是否加字段记录引用的线路id 复制线路
        unset($res['route_id']);
        $res['user_id'] = $userId;
        $res['route_created_at'] = time();
        $res['route_updated_at'] = time();
        $res = $this->route::create($res);

        //复制分类
        $this->addCid($routeId, $res);

        //复制行程
        $this->addDay($routeId, $res);

        //增加线路使用次数
        $this->route->where('route_id', '=', $routeId)->increment('route_use_num');
    }

    public function getData($routeId)
    {
        $data = $this->route->getInfo($routeId);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }
        $data['img'] = [];
        $data['day'] = [];

        //关联数据
        $dayJoin = $this->routeDayRepository->getDayData($routeId);
        if (false === empty($dayJoin)) {
            $data['day'] = $dayJoin;
            $data['img'] = $this->routeDayJoin::getAttractionsImgs($routeId);
        }

        return $data;

    }

    /**
     * @param $routeId
     * @param $value
     * @param $res
     * @param $dayAdd
     */
    private function addJoin($routeId, $value, $res, $dayAdd)
    {
        $join = $this->routeDayJoin::getJoinList($routeId, $value['route_day_id']);
        foreach ($join as $keyJ => $valueJ) {
            $arrJ = [
                'route_id' => $res->id,
                'route_day_id' => $dayAdd->id,
                'join_id' => $valueJ['join_id'],
                'route_day_join_sort' => $valueJ['route_day_join_sort'],
                'route_day_join_type' => $valueJ['route_day_join_type'],
            ];
            $this->routeDayJoin::create($arrJ);
        }
    }

    /**
     * @param $routeId
     * @param $res
     */
    private function addDay($routeId, $res)
    {
        $dayRes = $this->routeDay::getDayData($routeId);
        foreach ($dayRes as $key => $value) {
            $arr = [
                'route_id' => $res->id,
                'route_day_intro' => $value['route_day_intro'],
                'route_day_sort' => $value['route_day_sort'],

            ];
            $dayAdd = $this->routeDay::create($arr);

            //复制行程数据
            $this->addJoin($routeId, $value, $res, $dayAdd);
        }
    }

    /**
     * @param $routeId
     * @param $res
     */
    private function addCid($routeId, $res)
    {
        $cid = $this->cidMap::getCidsList($routeId, $this->cidMap::CID_MAP_TYPE_3);
        foreach ($cid as $key => &$value) {
            $value['join_id'] = $res->id;
            $this->cidMap::create($value);
        }
    }


}