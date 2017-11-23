<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Route;
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
    protected $route;

    public function __construct(
        Route $route,
        TokenService $tokenService,
        Request $request,
        RouteService $routeService)
    {

        parent::__construct();

        $this->route = $route;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->routeService = $routeService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //线路详情
    public function index($route_id = 0)
    {
        $routeId = $route_id ?? 0;
        $data = $this->routeService->getData($routeId);
        return response_success($data);
    }


    //使用线路
    public function use()
    {

        $this->params['route_id'] = $this->params['route_id'] ?? 0; //线路id
        $this->params['route_id'] = (int)$this->params['route_id'];


        $this->params['user_id'] = $this->params['user_id'] ?? 0; //用户id
        $this->params['user_id'] = (int)$this->params['user_id'];


        Log::error('使用线路参数: ', $this->params);


        if (!$this->params['user_id']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        if (!$this->params['route_id']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            //复制线路到用户
            $this->routeService->useRoute($this->params['route_id'],$this->params['user_id']);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('使用线路异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }



        return response_success(['msg' => '使用成功']);
    }

}
