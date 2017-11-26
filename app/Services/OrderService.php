<?php
/**
 * Created by PhpStorm.
 * Order: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Order;
use App\Models\Img;
use App\Models\Route;

class OrderService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $order;

    public function __construct(
        Order $order,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->order = $order;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }


    //获取用户订单统计信息
    public function getCountInfo($orderId)
    {
        $data = $this->order->countInfo($orderId);
        return $data;

    }
}