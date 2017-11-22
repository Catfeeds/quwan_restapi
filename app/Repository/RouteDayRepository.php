<?php

namespace App\Repository;


use App\Models\Attractions;
use App\Models\Cid;
use App\Models\CidMap;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Hotel;
use App\Models\RouteDay;
use App\Models\Img;
use App\Models\RouteDayJoin;

class RouteDayRepository extends BaseRepository
{

    //获取页面内容
    public  function getDayData($routeId){

        //获取所有行程信息
        $day = RouteDay::getDayData($routeId);
        if (true === empty($day)) {
            return [];
        }

        //获取线路下所有join信息
        $dayJoin = RouteDayJoin::getJoinData($routeId);

        foreach ($dayJoin as $key => &$value) {
            //'1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片',
            switch ((int)$value['route_day_join_type']) {
                case RouteDayJoin::ROUTE_DAY_JOIN_TYPE_A: //景点
                    $value['join_data'] = $this->getAttractionsList($value['join_id']);
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


        foreach ($day as $key => $value) {
            $day[$key]['day_data'] = [];
            foreach ($dayJoin as $keyJ => $valueJ) {
                if ((int)$value['route_day_id'] === (int)$valueJ['route_day_id'] && false === empty($valueJ['join_data'])) {
                    $day[$key]['day_data'][] = $valueJ['join_data'] ?? '';
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
        if (ture === empty($res)) {
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
        if (ture === empty($res)) {
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
