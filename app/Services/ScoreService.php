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
use App\Models\Holiday;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\Score;
use App\Models\Img;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $score;
    protected $user;
    protected $hotel;
    protected $holiday;
    protected $order;

    public function __construct(
        Order $order,
        Holiday $holiday,
        Hotel $hotel,
        User $user,
        Score $score,
        Hall $hall,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->order = $order;
        $this->holiday = $holiday;
        $this->hotel = $hotel;
        $this->user = $user;
        $this->score = $score;
        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    /**
     * 评价列表
     * @param $data
     * @return mixed|static
     */
    public function getList($data)
    {
        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;

        $query = $this->score::select('score_id', 'user_id', 'join_id', 'order_id', 'score', 'score_comment', 'score_type', 'score_status', 'score_created_at');
        $query->orderBy('score_id', 'DESC');

        $wheres = [];
        $condition = array(array('column' => 'score_status', 'value' => $this->score::SCORE_STATUS_1, 'operator' => '='));
        $wheres = array_merge($condition, $wheres);

        //1景点,2节日，3酒店,4餐厅
        if ($data['score_type']) {
            $condition = array(array('column' => 'score_type', 'value' => $data['score_type'], 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
        }

        if ($data['join_id']) {
            $condition = array(array('column' => 'join_id', 'value' => $data['join_id'], 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
        }

        //载入查询条件
        $wheres = array_reverse($wheres);
        foreach ($wheres as $value) {
            $query->where($value['column'], $value['operator'], $value['value']);
        }


        $result['_count'] = $query->count();
        $result['data'] = $query->skip($offset)->take($limit)->get()->toArray();

        if (false === empty($result['data'])) {
            foreach ($result['data'] as $key => &$value) {
                //获取用户头像昵称
                $userInfo = $this->user->getInfo($value['user_id']);
                $value['user_nickname'] = $userInfo['user_nickname'] ?? '';
                $value['user_avatar'] = $userInfo['user_avatar'] ?? '';

                //获取评论图片
                $value['img'] = $this->img::getJoinImgs($value['score_id'], $this->img::IMG_TYPE_E);

            }


        }

        return $result;

    }


    /**
     * @param $
     */
    public function addScore($params)
    {

        DB::connection('db_quwan')->beginTransaction();
        try {

            //添加评价
            $arr = [
                'user_id' => $params['user_id'],
                'join_id' => $params['join_id'],
                'order_id' => $params['order_id'],
                'score' => $params['score'],
                'score_comment' => $params['score_comment'],
                'score_type' => $params['score_type'],
                'score_created_at' => time(),
                'score_from_id' => $params['score_from_id'],
            ];
            $res = $this->score::create($arr);

            //添加评价图片
            if (false === empty($params['img'])) {
                foreach ($params['img'] as $key => $value) {
                    $arr = [
                        'join_id' => $res->id,
                        'img_sort' => $key + 1,
                        'img_type' => $this->img::IMG_TYPE_E,
                        'img_url' => $value,
                        'img_created_at' => time(),
                    ];
                    $this->img::create($arr);
                }
            }

            //最新平均评分
            $stock = $this->score::where('join_id','=', $params['join_id'])->where('score_type','=', $params['score_type'])->avg('score');
            $stock = round($stock,1);

            //评价酒店,餐厅,增加评价数量,更新评分
            //1景点,2目的地，3路线,4节日，5酒店,6餐厅,7图片
            switch ($params['score_type']) {
                case $this->score::SCORE_TYPE_A:
                    $this->attractions->where('attractions_id', '=', $params['join_id'])->increment('attractions_evaluation');
                    $this->attractions->where('attractions_id', '=', $params['join_id'])->increment('attractions_score_num');
                    $this->attractions->where('attractions_id', '=', $params['join_id'])->update(['attractions_score'=>$stock]);
                    break;
                case $this->score::SCORE_TYPE_B:
                    $this->attractions->where('attractions_id', '=', $params['join_id'])->increment('holiday_evaluation');
                    $this->holiday->where('holiday_id', '=', $params['join_id'])->increment('holiday_score_num');
                    $this->holiday->where('holiday_id', '=', $params['join_id'])->update(['holiday_score'=>$stock]);
                    break;
                case $this->score::SCORE_TYPE_C:
                    $this->hotel->where('hotel_id', '=', $params['join_id'])->increment('hotel_evaluation');
                    $this->hotel->where('hotel_id', '=', $params['join_id'])->increment('hotel_score_num');
                    $this->hotel->where('hotel_id', '=', $params['join_id'])->update(['hotel_score'=>$stock]);
                    break;
                case $this->score::SCORE_TYPE_D:
                    $this->hall->where('hall_id', '=', $params['join_id'])->increment('hall_evaluation');
                    $this->hall->where('hall_id', '=', $params['join_id'])->increment('hall_score_num');
                    $this->hall->where('hotel_id', '=', $params['join_id'])->update(['hall_score'=>$stock]);
                    break;
                default:
                    break;
            }

            //更改订单为已完成,如果评价中有订单号
            if(false === empty($params['order_id'])){
                $this->order->orderOK($params['order_id']);
            }

            DB::connection('db_quwan')->commit();


        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('添加评价异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }
    }
}