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
use App\Models\Score;
use App\Models\Img;
use App\Models\Route;
use App\Models\User;

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

    public function __construct(
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
     * 添加或取消收藏
     * @param $data
     * @return mixed|static
     */
    public function getList($data)
    {
        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;

        $query = $this->score::select('score_id','user_id','join_id','order_id','score','score_comment','score_type','score_status','score_created_at');
        $query->orderBy('score_id', 'DESC');

        $wheres = [];
        $condition = array(array('column' => 'score_status', 'value' => $this->score::SCORE_STATUS_1, 'operator' => '='));
        $wheres = array_merge($condition, $wheres);

        //1景点,2节日，3酒店,4餐厅
        if($data['score_type']){
            $condition = array(array('column' => 'score_type', 'value' => $data['score_type'], 'operator' => '='));
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
                $value['img'] = $this->img::getJoinImgs($value['score_id'], $this->img::IMG_TYPE_5);

            }


        }

        return $result;

    }
}