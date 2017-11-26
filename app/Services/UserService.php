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
use App\Models\User;
use App\Models\Img;
use App\Models\Route;

class UserService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $user;

    public function __construct(
        User $user,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->user = $user;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    //获取用户信息
    public function getUserInfo($userId)
    {
        $data = $this->user->getInfo($userId);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data;

    }

    //修改用户信息
    public function editUserInfo($userId,$params)
    {
        $data = $this->user->editInfo($userId,$params);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data;

    }

    //绑定用户手机
    public function bindMobile($userId,$userMobile)
    {
        $data = $this->user->bindMobile($userId,$userMobile);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data;

    }

    //修改用户经纬度
    public function editLbs($userId,$params)
    {
        $data = $this->user->editLbs($userId,$params);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data;

    }
}