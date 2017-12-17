<?php

namespace App\Repository;


use App\Models\Attractions;
use App\Models\Cid;
use App\Models\CidMap;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\RouteDay;
use App\Models\Img;
use App\Models\RouteDayJoin;

class RouteDayRepository extends BaseRepository
{


    //获取用户线路下未支付的商品
    public static  function getBuyGoodsData($routeId, $userId){

        //获取线路下所有join信息
        $dayJoin = RouteDayJoin::getJoinDataTo($routeId);

        $joinNum = count($dayJoin);
        $joinNumDuiBi = 0;
        foreach ($dayJoin as $key => $value) {
            // 检测支付状态
            $res = Order::checkUserOrder($userId, $value['join_id'],$value['route_day_join_type']);
            if($res){
                $joinNumDuiBi++;
            }
        }

        //0未支付,1已支付,2部分支付
        if(!$joinNumDuiBi){
            $status = 0;
        }else{
            if($joinNum === $joinNumDuiBi){
                $status = 1;
            }else if($joinNum > $joinNumDuiBi){
                $status = 2;
            }
        }

        // var_dump($joinNum, $joinNumDuiBi);die;

        return $status;
    }

    //获取线路下所有景点,节日
    public static  function getDayGoodsData($routeId, $userId){

        //获取线路下所有join信息
        $dayJoin = RouteDayJoin::getJoinDataTo($routeId);

        $joinNum = count($dayJoin);
        $joinNumDuiBi = 0;
        foreach ($dayJoin as $key => $value) {
            // 检测支付状态
            $res = Order::checkUserOrder($userId, $value['join_id'],$value['route_day_join_type']);
            if($res){
                $joinNumDuiBi++;
            }
        }

        //0未支付,1已支付,2部分支付
        if(!$joinNumDuiBi){
            $status = 0;
        }else{
            if($joinNum === $joinNumDuiBi){
                $status = 1;
            }else if($joinNum > $joinNumDuiBi){
                $status = 2;
            }
        }

        // var_dump($joinNum, $joinNumDuiBi);die;

        return $status;
    }

    //获取页面内容
    public  function getDayData($routeId, $userId){

        //获取所有行程信息
        $day = RouteDay::getDayData($routeId);
        if (true === empty($day)) {
            return [];
        }

        //获取线路下所有join信息
        $dayJoin = RouteDayJoin::getJoinData($routeId);

        foreach ($dayJoin as $key => &$value) {
            //支付状态 [0未支付,1已支付,2部分支付]
            $value['pay_status'] = 0;

            //'1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片',
            switch ((int)$value['route_day_join_type']) {
                case RouteDayJoin::ROUTE_DAY_JOIN_TYPE_A: //景点
                    $value['join_data'] = $this->getAttractionsList($value['join_id']);

                    //支付信息
                    if($userId){
                        $tag = Order::checkUserOrder($userId, $value['join_id'],$value['route_day_join_type']);
                        $value['pay_status'] = $tag ? 1 : 0;
                    }

                    break;
                case RouteDayJoin::ROUTE_DAY_JOIN_TYPE_D: //酒店
                    $value['join_data'] = $this->getHotelList($value['join_id']);
                    break;
                case RouteDayJoin::ROUTE_DAY_JOIN_TYPE_E: //餐厅
                    $value['join_data'] = $this->getHallList($value['join_id']);
                    break;
                default:
                    $value['join_data'] = $this->getAttractionsList($value['join_id']);
                    break;
            }
        }

        $dayJoinNew = $dayJoin;

        foreach ($dayJoinNew as $keyJ => $valueJ) {
            foreach ($day as $keyB => $valueB) {
                if((int)$valueB['route_day_id'] === (int)$valueJ['route_day_id']){
                    $day[$keyB]['day_data'][] = $valueJ;
                }
            }

        }
        return $day;
    }


    /**
     * @param $hotelId
     * @return array
     */
    private function getHotelList($hotelId)
    {
        $res = Hotel::getInfo($hotelId);
        if (true === empty($res)) {
            return [];
        }

        $img = Img::getOneImg($hotelId, Img::IMG_TYPE_C);
        $data = [
            'join_type' => RouteDayJoin::ROUTE_DAY_JOIN_TYPE_D,
            'join_id' => $res['hotel_id'],
            'join_name' => $res['hotel_name'],
            'join_intro' => $res['hotel_intro'],
            'join_suggest' => '',
            'join_score' => $res['hotel_score'],
            'join_evaluation' => $res['hotel_evaluation'],
            'join_img' => $img,
        ];

        return $data;
    }


    /**
     * @param $attractionsId
     * @return array
     */
    private function getAttractionsList($attractionsId)
    {
        $res = Attractions::getInfo($attractionsId);
        if (true === empty($res)) {
            return [];
        }

        $img = Img::getOneImg($attractionsId, Img::IMG_TYPE_A);
        $data = [
            'join_type' => RouteDayJoin::ROUTE_DAY_JOIN_TYPE_A,
            'join_id' => $res['attractions_id'],
            'join_name' => $res['attractions_name'],
            'join_intro' => $res['attractions_intro'],
            'join_suggest' => $res['attractions_suggest'],
            'join_score' => $res['attractions_score'],
            'join_evaluation' => $res['attractions_evaluation'],
            'join_img' => $img,
        ];

        return $data;
    }

    /**
     * @param $hallId
     * @return array
     */
    private function getHallList($hallId)
    {

        $res = Hall::getInfo($hallId);
        if (true === empty($res)) {
            return [];
        }

        $img = Img::getOneImg($hallId, Img::IMG_TYPE_D);
        $data = [
            'join_type' => RouteDayJoin::ROUTE_DAY_JOIN_TYPE_E,
            'join_id' => $res['hall_id'],
            'join_name' => $res['hall_name'],
            'join_intro' => $res['hall_intro'],
            'join_suggest' => '',
            'join_score' => $res['hall_score'],
            'join_evaluation' => $res['hall_evaluation'],
            'join_img' => $img,
        ];


        return $data;
    }

}
