<?php

namespace App\Repository;


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

class HomePageRepository extends BaseRepository
{

    //获取页面内容
    public  function getPageData($data,$userId){
        $res = [
            'adv'=>[],
            'route'=>[],
            'destination'=>[],
            'cid'=>[],
            'attractions'=>[],
            'holiday'=>[],
            'nearby'=>[],
        ];
        foreach ($data as $key => $value) {

            //1首页广告 2首页热门线路 3首页热门目的地 4首页景点 5首页节日 6首页推荐周边 7景点分类
            switch ($value['home_page_type']) {
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
                    $res = $this->getAttractionsList($value, $res,$userId);
                    break;
                case HomePage::PAGE_TYPE_5: //节日
                    $res = $this->getHolidayList($value, $res);
                    break;
                case HomePage::PAGE_TYPE_6: //周边
                    $res = $this->getNearbyList($value, $res,$userId);
                    break;
                case HomePage::PAGE_TYPE_7: //景点分类
                    $res = $this->getCidList($value, $res);
                    break;
                default: break;
            }
        }

        return $res;
    }

    /**
     * @param $value
     * @param $res
     * @return mixed
     */
    private function getAdvList($value, $res)
    {
        $res['adv'] = HomePageValue::select('a.adv_id', 'a.adv_title', 'a.adv_url', 'a.adv_type', 'a.adv_img', 'a.adv_content')
            ->leftJoin('adv as a', 'a.adv_id', '=', 'home_page_value.value_id')
            ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
            ->orderBy('home_page_value.sort')
            ->get()
            ->toArray();
        return $res;
    }

    /**
     * @param $value
     * @param $res
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

        if (false === empty($res['route'])) {
            foreach ($res['route'] as $keyR => &$valueR) {
                //图片
                $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['route_id']);
                //分类
                $valueR['cid'] = CidMap::getCidsInfo($valueR['route_id'], CidMap::CID_MAP_TYPE_3);

                $valueR['route_intro'] = htmlspecialchars_decode($valueR['route_intro']);
            }

        }
        return $res;
    }

    /**
     * @param $value
     * @param $res
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


        if (false === empty($res['destination'])) {
            foreach ($res['destination'] as $keyD => &$valueD) {
                //图片
                $valueD['img'] = DestinationJoin::getOneJoinImg($valueD['destination_id']);

            }

        }
        return $res;
    }

    /**
     * @param $value
     * @param $res
     * @return mixed
     */
    private function getCidList($value, $res)
    {
        $res['cid'] = HomePageValue::select('a.cid_id', 'a.cid_name')
            ->leftJoin('cid as a', 'a.cid_id', '=', 'home_page_value.value_id')
            ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
            ->where('a.cid_type', '=', Cid::CID_TYPE_A)
            ->orderBy('home_page_value.sort')
            ->get()
            ->toArray();
        return $res;
    }

    /**
     * @param $value
     * @param $res
     * @return array
     */
    private function getAttractionsList($value, $res,$userId)
    {
        $res['attractions'] = HomePageValue::select('a.attractions_id', 'a.attractions_name', 'a.attractions_intro', 'a.attractions_price', 'a.attractions_score', 'a.attractions_evaluation', 'a.attractions_lon', 'a.attractions_lat','a.attractions_suggest')
            ->leftJoin('attractions as a', 'a.attractions_id', '=', 'home_page_value.value_id')
            ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
            ->orderBy('home_page_value.sort')
            ->get()
            ->toArray();

        $userLon = User::getUserLon($userId);


        if (false === empty($res['attractions'])) {
            foreach ($res['attractions'] as $keyA => &$valueA) {

                $valueA['distance'] = 0;

                //距离
                if(false === empty($userLon)){
                    $valueA['distance'] =  get_distance($userLon['user_lon'], $userLon['user_lat'], $valueA['attractions_lon'], $valueA['attractions_lat']);
                }

                //图片
                $valueA['img'] = Img::getOneImg($valueA['attractions_id'], Img::IMG_TYPE_A);
                //分类
                $valueA['cid'] = CidMap::getCidsInfo($valueA['attractions_id'], CidMap::CID_MAP_TYPE_1);

                $valueA['attractions_intro'] = htmlspecialchars_decode($valueA['attractions_intro']);

            }

        }
        return $res;
    }

    /**
     * @param $value
     * @param $res
     * @return array
     */
    private function getHolidayList($value, $res)
    {
        $res['holiday'] = HomePageValue::select('a.holiday_id', 'a.holiday_name','a.holiday_suggest')
            ->leftJoin('holiday as a', 'a.holiday_id', '=', 'home_page_value.value_id')
            ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
            ->orderBy('home_page_value.sort')
            ->get()
            ->toArray();

        if (false === empty($res['holiday'])) {
            foreach ($res['holiday'] as $keyH => &$valueH) {
                //图片
                $valueH['img'] = Img::getOneImg($valueH['holiday_id'], Img::IMG_TYPE_B);
            }
        }
        return $res;
    }

    /**
     * @param $value
     * @param $res
     * @return array
     */
    private function getNearbyList($value, $res,$userId)
    {
        //获取用户经纬度
        if(!$userId){
            return $res;
        }

        $userLon = User::getUserLon($userId);
        if(true === empty($userLon)){
            return $res;
        }

        $nearby = HomePageValue::select('value_id')
            ->where('home_page_value.home_page_id', '=', $value['home_page_id'])
            ->orderBy('home_page_value.sort')
            ->get()
            ->toArray();


        if (false === empty($nearby)) {

            //推荐线路
            $routeId = array_shift($nearby);
            $route = Route::select('route_id', 'route_name', 'route_name', 'route_day_num', 'route_intro')
                ->first($routeId);
            if (false === empty($route)) {
                $route = $route->toArray();
                //图片
                $route['img'] = RouteDayJoin::getOneJoinImg($route['route_id']);
                //分类
                $route['cid'] = CidMap::getCidsInfo($route['route_id'], CidMap::CID_MAP_TYPE_3);
            }
            $res['nearby']['route'] = $route;


            $attractions = Attractions::select('attractions_id', 'attractions_name', 'attractions_intro', 'attractions_price', 'attractions_score', 'attractions_evaluation', 'attractions_lon', 'attractions_lat')
                ->whereIn('attractions_id', $nearby)->get()->toArray();
            if (false === empty($attractions)) {
                foreach ($attractions as $keyAAA => &$valueAAA) {
                    //距离
                    $valueAAA['distance'] =  get_distance($userLon['user_lon'], $userLon['user_lat'], $valueAAA['attractions_lon'], $valueAAA['attractions_lat']);

                    //图片
                    $valueAAA['img'] = Img::getOneImg($valueAAA['attractions_id'], Img::IMG_TYPE_A);
                    //分类
                    $valueAAA['cid'] = CidMap::getCidsInfo($valueAAA['attractions_id'], CidMap::CID_MAP_TYPE_1);
                }

            }

            $res['nearby']['attractions'] = $attractions;
        }
        return $res;
    }


}
