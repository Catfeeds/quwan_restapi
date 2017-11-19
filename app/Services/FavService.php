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
use App\Models\Img;
use App\Models\Route;

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

    public function __construct(
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


        $this->fav = $fav;
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
    public function addOrDel($data)
    {
        $favId = $this->fav->isFav($data['fav_type'],$data['user_id'],$data['join_id']);
        if ($favId) {
            $this->fav::where('fav_id','=',$favId)->delete();
            return $favId;
        }

        $favId = $this->fav->add($data);

        return $favId;

    }
}