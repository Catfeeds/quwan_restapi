<?php

namespace App\Http\Controllers\V1;


use App\Services\RouteService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class RouteController
 * @package App\Http\Controllers\V1
 */
class RouteController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $routeService;

    public function __construct(TokenService $tokenService, Request $request,RouteService $routeService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->routeService = $routeService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index($route_id = 0)
    {
        $routeId = $route_id ?? 0;
        $data = $this->routeService->getData($routeId);
        return response_success($data);
    }

}
