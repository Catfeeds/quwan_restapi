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
use App\Models\Suggest;
use App\Models\Img;
use App\Models\Route;

class SuggestService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $suggest;

    public function __construct(
        Suggest $suggest,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->suggest = $suggest;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    public function addSuggest($params)
    {
        //添加
        $arr = [
            'user_id' => $params['user_id'],
            'suggest_comment' => $params['suggest_comment'],
            'suggest_phone' => $params['suggest_phone'],
            'suggest_created_at' => time(),
        ];
        $res = $this->suggest::create($arr);

        //添加评价图片
        if (false === empty($params['img'])) {
            foreach ($params['img'] as $key => $value) {
                $arr = [
                    'join_id' => $res->id,
                    'img_sort' => $key + 1,
                    'img_type' => $this->img::IMG_TYPE_F,
                    'img_url' => $value,
                    'img_created_at' => time(),
                ];
                $this->img::create($arr);
            }
        }

    }
}