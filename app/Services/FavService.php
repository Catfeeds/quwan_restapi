<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Fav;
use App\Models\Holiday;
use App\Models\Hotel;
use App\Models\Img;
use App\Models\Route;
use App\Models\User;

class FavService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $fav;
    protected $hotel;
    protected $holiday;
    protected $user;

    public function __construct(
        User $user,
        Holiday $holiday,
        Hotel $hotel,
        Fav $fav,
        Hall $hall,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->user = $user;
        $this->holiday = $holiday;
        $this->hotel = $hotel;
        $this->fav = $fav;
        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    //检测用户是否收藏
    public function isFav($favType, $userId, $joinId)
    {
        return $this->fav->isFav($favType,$userId,$joinId);
    }

    /**
     * 添加或取消收藏
     * @param $data
     * @return mixed|static
     */
    public function addOrDel($data)
    {
        $msg = '收藏成功';
        $favId = $this->isFav($data['fav_type'],$data['user_id'],$data['join_id']);
        if ($favId) {
            $this->fav::where('fav_id','=',$favId)->delete();
            $msg = '取消收藏成功';
            return $msg;
        }

        $favId = $this->fav->add($data);

        return $msg;

    }

    /**
     * 收藏列表
     * @param $data
     * @return mixed|static
     */
    public function getListData($data)
    {
        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;

        $query = $this->fav::select('fav_id', 'join_id', 'fav_type', 'user_id', 'fav_created_at');
        $query->orderBy('fav_id', 'DESC');

        $wheres = [];
        $condition = array(array('column' => 'user_id', 'value' => $data['user_id'], 'operator' => '='));
        $wheres = array_merge($condition, $wheres);

        //1景点,2节日，3酒店,4餐厅
        if ($data['fav_type']) {
            $condition = array(array('column' => 'fav_type', 'value' => $data['fav_type'], 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
        }

        //载入查询条件
        $wheres = array_reverse($wheres);
        foreach ($wheres as $value) {
            $query->where($value['column'], $value['operator'], $value['value']);
        }

        $result['_count'] = $query->count();
         $result['data'] = $query->skip($offset)->take($limit)->get()->toArray();

        $res = [];
        if (false === empty($result['data'])) {
            //获取用户经纬度
            $userInfo = $this->user->getInfo($data['user_id']);

            foreach ($result['data'] as $key => $value) {
                //1景点,2目的地，3路线,4节日，5酒店,6餐厅
                switch ((int)$value['fav_type']) {
                    case $this->fav::FAV_TYPE_A: //景点
                        $info = $this->getAttractionData($value['join_id']);
                        break;
                    case $this->fav::FAV_TYPE_B: //节日
                        $info = $this->getHolidayData($value['join_id']);
                        break;
                    case $this->fav::FAV_TYPE_C: //酒店
                        $info = $this->getHotelData($value['join_id']);
                        break;
                    case $this->fav::FAV_TYPE_D: //餐厅
                        $info = $this->getHallData($value['join_id']);
                        break;
                    default:
                        $info = [];
                        break;
                }

                if(false === empty($info)){
                    //计算距离
                    $info['distance'] = round(get_distance(
                        $userInfo['user_lon'], $userInfo['user_lat'],
                        $info['lon'], $info['lat']
                    ));

                    //@todo 注意图片url处理
                    $info['img'] = $info['img'][0] ?? '';
                    $res[] = $info;

                }

            }
        }

        $result['data'] = $res;

        return $result;

    }

    /**
     * 景点数据
     * @param $value
     * @return array
     */
    public function getAttractionData($attractionsId)
    {
        $info = $this->attractions::getInfo($attractionsId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'join_id' => $info['attractions_id'],
                'type' => $this->fav::FAV_TYPE_A,
                'name' => $info['attractions_name'],
                'img' => $info['img'],
                'price' => $info['attractions_price'],
                'intro' => $info['attractions_intro'],
                'score' => $info['attractions_score'],
                'evaluation' => $info['attractions_evaluation'],
                'lon' => $info['attractions_lon'],
                'lat' => $info['attractions_lat'],
            ];
        }
        return $res;
    }

    /**
     * 节日数据
     * @param $value
     * @return array
     */
    public function getHolidayData($holidayId)
    {
        $info = $this->holiday->getInfo($holidayId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'join_id' => $info['holiday_id'],
                'type' => $this->fav::FAV_TYPE_B,
                'name' => $info['holiday_name'],
                'img' => $info['img'],
                'price' => $info['holiday_price'],
                'intro' => $info['holiday_intro'],
                'score' => $info['holiday_score'],
                'evaluation' => $info['holiday_evaluation'],
                'lon' => $info['holiday_lon'],
                'lat' => $info['holiday_lat'],
            ];
        }
        return $res;
    }

    /**
     * 酒店数据
     * @param $value
     * @return array
     */
    public function getHotelData($hotelId)
    {
        $info = $this->hotel::getInfo($hotelId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'join_id' => $info['hotel_id'],
                'type' => $this->fav::FAV_TYPE_C,
                'name' => $info['hotel_name'],
                'img' => $info['img'],
                'price' => $info['hotel_price'],
                'intro' => $info['hotel_intro'],
                'score' => $info['hotel_score'],
                'evaluation' => $info['hotel_evaluation'],
                'lon' => $info['hotel_lon'],
                'lat' => $info['hotel_lat'],
            ];
        }
        return $res;
    }

    /**
     * 餐厅数据
     * @param $value
     * @return array
     */
    public function getHallData($hallId)
    {
        $info = $this->hall::getInfo($hallId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'join_id' => $info['hall_id'],
                'type' => $this->fav::FAV_TYPE_D,
                'name' => $info['hall_name'],
                'img' => $info['img'],
                'price' => $info['hall_price'],
                'intro' => $info['hall_intro'],
                'score' => $info['hall_score'],
                'evaluation' => $info['hall_evaluation'],
                'lon' => $info['hall_lon'],
                'lat' => $info['hall_lat'],
            ];
        }
        return $res;
    }

}