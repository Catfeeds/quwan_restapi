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

    //删除线路
    public function delRoute($routeId){
        $tag = $this->route->where('route_id','=',$routeId)->delete();
        if ($tag) {
            $this->routeDay->where('route_id','=',$routeId)->delete();
            $this->routeDayJoin->where('route_id','=',$routeId)->delete();
        }
    }

    //复制线路到用户
    public function useRoute($routeId, $userId)
    {
        //检测线路是否存在
        $res = $this->checkRoute($routeId);

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

    public function getData($routeId, $userId = 0)
    {
        $data = $this->route->getInfo($routeId);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        $data['img'] = [];
        $data['day'] = [];

        //关联数据
        $dayJoin = $this->routeDayRepository->getDayData($routeId, $userId);
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

    /**
     * 我的线路
     * @param $params
     * @return array
     */
    public function getList($params)
    {
       $params['limit'] =$params['limit'] ?? 10;//每页显示数
       $params['limit'] = (int)$params['limit'];

       $params['offset'] =$params['offset'] ?? 1;//页码
       $params['offset'] = (int)$params['offset'];

       $params['user_id'] =$params['user_id'] ?? 0;
       $params['user_id'] = (int)$params['user_id'];


        $data = $this->route::getUserListInfo($params);

        if (!$data) {
            $data = [
                'paging' => [
                    'limit' => 10,
                    'offset' => 0,
                    'total' => 0,
                ],
                'data' => []
            ];
        }

        return $data;
    }

    /**
     * 创建线路
     */
    public function addRoute($params)
    {

        $route['user_id'] = $params['user_id'];
        $route['route_name'] = $params['route_name'];
        $route['route_day_num'] = $params['route_day_num'];
        $route['route_intro'] = $params['route_intro'];
        $route['route_created_at'] = time();
        $res = $this->route::create($route);
        if(true === $res->id){
            throw new UnprocessableEntityHttpException(850035);
        }
        $routeId = $res->id;


        //添加分类
        $this->addRouteCid($routeId, $params);

        //添加行程
        $this->addRouteDay($routeId, $params);
    }

    /**
     * 编辑线路
     */
    public function editRoute($params)
    {

        $route['user_id'] = $params['user_id'];
        $route['route_name'] = $params['route_name'];
        $route['route_day_num'] = $params['route_day_num'];
        $route['route_intro'] = $params['route_intro'];
        $route['route_updated_at'] = time();
        $routeId = $params['route_id'];
        $res = $this->route::where('route_id','=',$routeId)->update($route);

        //删除线路相关分类,相关行程,相关行程数据
        $this->cidMap::where('join_id','=',$routeId)->where('cid_map_type','=',CidMap::CID_MAP_TYPE_3)->delete();
        $this->routeDay::where('route_id','=',$routeId)->delete();
        $this->routeDayJoin::where('route_id','=',$routeId)->delete();

        //添加分类
        $this->addRouteCid($routeId, $params);

        //添加行程
        $this->addRouteDay($routeId, $params);
    }

    /**
     * 添加线路分类
     * @param $routeId
     * @param $params
     */
    private function addRouteCid($routeId, $params)
    {
        if(false === empty($params['cid'])){
            foreach ($params['cid'] as $key => $value) {
                $arr = [
                    'cid_id' => $value,
                    'join_id' => $routeId,
                    'cid_map_sort' => $key+1,
                    'cid_map_type' => CidMap::CID_MAP_TYPE_3,
                ];
                $this->cidMap::create($arr);
            }
        }

    }

    /**
     * 添加线路行程
     * @param $routeId
     * @param $params
     */
    private function addRouteDay($routeId, $params)
    {
        if(false === empty($params['day'])){
            foreach ($params['day'] as $key => $value) {

                $arr = [
                    'route_id' => $routeId,
                    'route_day_intro' => $value['route_day_intro'],
                    'route_day_sort' => $key+1,

                ];
                $dayAdd = $this->routeDay::create($arr);
                if(true === $dayAdd->id){
                    throw new UnprocessableEntityHttpException(850036);
                }

                //复制行程数据
                $this->addRouteDayJoin($routeId, $value['day_data'], $dayAdd->id);
            }
        }

    }

    /**
     * 添加行程数据
     * @param $routeId
     * @param $params
     * @param $dayId
     */
    private function addRouteDayJoin($routeId, $params, $dayId)
    {
        if(false === empty($params)){
            foreach ($params as $keyJ => $valueJ) {
                $arrJ = [
                    'route_id' => $routeId,
                    'route_day_id' => $dayId,
                    'join_id' => $valueJ['join_id'],
                    'route_day_join_sort' => $keyJ+1,
                    'route_day_join_type' => $valueJ['join_type'],
                ];
                $res = $this->routeDayJoin::create($arrJ);
                if(true === $res->id){
                    throw new UnprocessableEntityHttpException(850037);
                }
            }
        }

    }

    /**
     * @param $routeId
     * @return mixed
     */
    public function checkRoute($routeId)
    {
        $res = $this->route->where('route_id', '=', $routeId)->first();
        if (!$res) {
            throw new UnprocessableEntityHttpException(850004);
        }
        $res = $res->toArray();
        return $res;
    }
}