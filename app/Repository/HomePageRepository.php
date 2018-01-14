<?php

namespace App\Repository;


use App\Models\Adv;
use App\Models\Attractions;
use App\Models\Cid;
use App\Models\CidMap;
use App\Models\DestinationJoin;
use App\Models\HomePage;
use App\Models\HomePageValue;
use App\Models\Img;
use App\Models\Route;
use App\Models\RouteDayJoin;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomePageRepository extends BaseRepository
{
    const NEARBY_DISTANCE = 200000; //200公里 推荐范围

    //获取页面内容
    public function getPageData($data, $userId)
    {
        $res = [
            'adv'         => [],
            'route'       => [],
            'destination' => [],
            'cid'         => [],
            'attractions' => [],
            'holiday'     => [],
            'nearby'      => [],
        ];
        foreach ($data as $key => $value)
        {

            //1首页广告 2首页热门线路 3首页热门目的地 4首页景点 5首页节日 6首页推荐周边 7景点分类
            switch ($value['home_page_type'])
            {
                case HomePage::PAGE_TYPE_1: //广告
                    $res = $this->getAdvList($value, $res);
                    break;
                case HomePage::PAGE_TYPE_2: //线路
                    $res = $this->getRouteList($value, $res);
                    break;
                case HomePage::PAGE_TYPE_3: //目的地
                    $res = $this->getDestinationList($value, $res);
                    break;
                case HomePage::PAGE_TYPE_4: //景点
                    $res = $this->getAttractionsList($value, $res, $userId);
                    break;
                case HomePage::PAGE_TYPE_5: //节日
                    $res = $this->getHolidayList($value, $res);
                    break;
                case HomePage::PAGE_TYPE_6: //周边
                    $res = $this->getNearbyList($value, $res, $userId);
                    break;
                case HomePage::PAGE_TYPE_7: //景点分类
                    $res = $this->getCidList($value, $res);
                    break;
                default:
                    break;
            }
        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return mixed
     */
    private function getAdvList($value, $res)
    {
        //$res['adv'] = HomePageValue::select('a.adv_id', 'a.adv_title', 'a.adv_url', 'a.adv_type', 'a.adv_img', 'a.adv_content')
        //                           ->leftJoin('adv as a', 'a.adv_id', '=', 'home_page_value.value_id')
        //                           ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
        //                           ->orderBy('home_page_value.sort')
        //                           ->get()
        //                           ->toArray();

        $res['adv'] = Adv::select('adv_id', 'adv_title', 'adv_url', 'adv_type', 'adv_img')
                         ->where('adv_status', '=', Adv::ADV_STATUS_1)->orderBy('adv_weight')->get()->toArray();

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return array
     */
    private function getRouteList($value, $res)
    {
        $res['route'] = HomePageValue::select('a.route_id', 'a.route_name', 'a.route_name', 'a.route_day_num', 'a.route_intro')
                                     ->leftJoin('route as a', 'a.route_id', '=', 'home_page_value.value_id')
                                     ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
                                     ->orderBy('home_page_value.sort')
                                     ->get()
                                     ->toArray();

        if (false === empty($res['route']))
        {
            foreach ($res['route'] as $keyR => &$valueR)
            {
                //图片
                $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['route_id']);
                //分类
                $valueR['cid'] = CidMap::getCidsInfo($valueR['route_id'], CidMap::CID_MAP_TYPE_3);

                $valueR['route_intro'] = htmlspecialchars_decode($valueR['route_intro']);

                $valueR['route_intro'] = lose_space(strip_tags($valueR['route_intro']));
            }

        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return array
     */
    private function getDestinationList($value, $res)
    {
        $res['destination'] = HomePageValue::select('a.destination_id', 'a.destination_name')
                                           ->leftJoin('destination as a', 'a.destination_id', '=', 'home_page_value.value_id')
                                           ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
                                           ->orderBy('home_page_value.sort')
                                           ->get()
                                           ->toArray();


        if (false === empty($res['destination']))
        {
            foreach ($res['destination'] as $keyD => &$valueD)
            {
                //图片
                $valueD['img'] = DestinationJoin::getOneJoinImg($valueD['destination_id']);

            }

        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return mixed
     */
    private function getCidList($value, $res)
    {
        $res['cid'] = HomePageValue::select('a.cid_id', 'a.cid_name')
                                   ->leftJoin('cid as a', 'a.cid_id', '=', 'home_page_value.value_id')
                                   ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
                                   ->where('a.cid_type', '=', Cid::CID_TYPE_A)
                                   ->orderBy('home_page_value.sort')
                                   ->groupBy('a.cid_id')
                                   ->get()
                                   ->toArray();

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return array
     */
    private function getAttractionsList($value, $res, $userId)
    {
        $res['attractions'] = HomePageValue::select('a.attractions_id', 'a.attractions_name', 'a.attractions_intro', 'a.attractions_price', 'a.attractions_score', 'a.attractions_evaluation', 'a.attractions_lon', 'a.attractions_lat', 'a.attractions_suggest')
                                           ->leftJoin('attractions as a', 'a.attractions_id', '=', 'home_page_value.value_id')
                                           ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
                                           ->orderBy('home_page_value.sort')
                                           ->get()
                                           ->toArray();

        $userLon = User::getUserLon($userId);


        if (false === empty($res['attractions']))
        {
            foreach ($res['attractions'] as $keyA => &$valueA)
            {

                $valueA['distance'] = 0;

                //距离
                if (false === empty($userLon))
                {
                    $valueA['distance'] = get_distance($userLon['user_lon'], $userLon['user_lat'], $valueA['attractions_lon'], $valueA['attractions_lat']);
                }

                //图片
                $valueA['img'] = Img::getOneImg($valueA['attractions_id'], Img::IMG_TYPE_A);
                //分类
                $valueA['cid'] = CidMap::getCidsInfo($valueA['attractions_id'], CidMap::CID_MAP_TYPE_1);

                //描述简介处理
                $valueA['attractions_intro'] = htmlspecialchars_decode($valueA['attractions_intro']);
                $valueA['attractions_intro'] = lose_space(strip_tags($valueA['attractions_intro']));
            }

        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return array
     */
    private function getHolidayList($value, $res)
    {
        $res['holiday'] = HomePageValue::select('a.holiday_id', 'a.holiday_name', 'a.holiday_suggest')
                                       ->leftJoin('holiday as a', 'a.holiday_id', '=', 'home_page_value.value_id')
                                       ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
                                       ->orderBy('home_page_value.sort')
                                       ->get()
                                       ->toArray();

        if (false === empty($res['holiday']))
        {
            foreach ($res['holiday'] as $keyH => &$valueH)
            {
                //图片
                $valueH['img'] = Img::getOneImg($valueH['holiday_id'], Img::IMG_TYPE_B);
            }
        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     *
     * @return array
     */
    private function getNearbyList($value, $res, $userId)
    {
        //获取用户经纬度
        if (!$userId)
        {
            return $res;
        }

        $userLon = User::getUserLon($userId);
        if (true === empty($userLon))
        {
            return $res;
        }


        ////所以对于这个例子,我增加了4个where条件,只对于经度和纬度大于或小于该用户1度(111公里)范围内的用户进行距离计算,同时对数据表中的经度和纬度两个列增加了索引来优化where语句执行时的速度.
        //
        ////根据用户经纬度,取出销售量最好,方圆111十公里内最近的一条数据
        //$lat = $userLon['user_lat'];
        //$lon = $userLon['user_lon'];
        //$sql = ' SELECT * FROM qw_attractions
        //    WHERE
        //        attractions_lat > ' . $lat . ' - 1
        //    AND attractions_lat < ' . $lat . ' + 1
        //    AND attractions_lon > ' . $lon . ' - 1
        //    AND attractions_lon < ' . $lon . ' + 1
        //    ORDER BY attractions_sales_num DESC,
        //        ACOS(
        //            SIN((' . $lat . ' * 3.1415) / 180) * SIN((attractions_lat * 3.1415) / 180) + COS((' . $lat . ' * 3.1415) / 180) * COS((attractions_lat * 3.1415) / 180) * COS(
        //                (' . $lon . ' * 3.1415) / 180 - (attractions_lon * 3.1415) / 180
        //            )
        //        ) * 6380 ASC
        //    LIMIT 1';
        //
        //
        //$list = DB::connection('db_quwan')
        //          ->select($sql);
        //
        //$Lbs = new  LbsRepository();
        //foreach ($list as $key => &$value)
        //{
        //    //格式化距离
        //    $value->juli = $Lbs->nearbyDistance($lat, $lon, $value->attractions_lat, $value->attractions_lon);
        //}
        //
        //var_dump($list);
        //die;





        //==========================




            //推荐线路
            $list = Route::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro','route_lon as lon','route_lat as lat')
                ->where('route_status','=',Route::ROUTE_STATUS_1)->orderBy('route_use_num')->get()->toArray();
            $Lbs = new  LbsRepository();
            $result = $Lbs->range($userLon['user_lat'],$userLon['user_lon'],$list);
            $route = [];
            if(false === empty($result)){
                $tmp = [];
                foreach ($result as $key => $value)
                {
                    //附近200公里内使用量（用户可以使用一条路线，称之为使用量）最大的路线
                    if($value['distance'] <= self::NEARBY_DISTANCE){
                        $tmp[] = $value;
                    }
                }
                $tmp = new_array_sort($tmp, 'attractions_sales_num', 'desc');
                $route = $tmp[0] ?? [];
            }

            if (false === empty($route))
            {
                //图片
                $route['img'] = RouteDayJoin::getOneJoinImg($route['route_id']);
                //分类
                $route['cid'] = CidMap::getCidsInfo($route['route_id'], CidMap::CID_MAP_TYPE_3);
            }
            $res['nearby']['route'] = $route;




            //推荐景点
            $listA = Attractions::select('attractions_id', 'attractions_name', 'attractions_name', 'attractions_sales_num',  'attractions_price', 'attractions_score', 'attractions_evaluation', 'attractions_lon as lon', 'attractions_lat as lat')
                ->where('attractions_status','=',Attractions::ATTRACTIONS_STATUS_1)
                ->orderBy('attractions_sales_num')->get()->toArray();
            $Lbs = new  LbsRepository();
            $resultA = $Lbs->range($userLon['user_lat'],$userLon['user_lon'],$listA);
            $attractions = [];
            if(false === empty($resultA)){
                $tmp = [];
                foreach ($resultA as $key => $value)
                {
                    //附近200公里内使用量（用户可以使用一条路线，称之为使用量）最大的路线
                    if($value['distance'] <= self::NEARBY_DISTANCE){
                        $tmp[] = $value;
                    }
                }

                $tmp = new_array_sort($tmp, 'attractions_sales_num', 'desc');
                if(false === empty($tmp)){
                    foreach ($tmp as $key => $value) {
                        if($key <= 1){
                            $attractions[] = $value;
                        }
                    }
                }
            }

            if (false === empty($attractions))
            {
                foreach ($attractions as $keyAAA => &$valueAAA)
                {

                    //图片
                    $valueAAA['img'] = Img::getOneImg($valueAAA['attractions_id'], Img::IMG_TYPE_A);
                    //分类
                    $valueAAA['cid'] = CidMap::getCidsInfo($valueAAA['attractions_id'], CidMap::CID_MAP_TYPE_1);
                }

            }

            $res['nearby']['attractions'] = $attractions;


        return $res;
    }


}
