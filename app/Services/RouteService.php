<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Route;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Img;

class RouteService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $route;
    protected $cidMap;
    protected $hall;

    public function __construct(
        Hall $hall,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Route $route
    )
    {


        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->route = $route;

    }

    public function getData($routeId)
    {
        $data = $this->route->getInfo($routeId);
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data;

    }


}