<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Route;
use App\Repository\RouteDayRepository;
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
        $userId = $this->userId ?? 0;

        $routeId = $route_id ?? 0;
        $data = $this->routeService->getData($routeId, $userId);
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


    //添加线路
    public function add()
    {

        $this->params['route_name'] = $this->params['route_name'] ?? ''; //线路名称
        $this->params['route_intro'] = $this->params['route_intro'] ?? ''; //线路介绍
        $this->params['route_day_num'] = $this->params['route_day_num'] ?? 0; //线路天数
        $this->params['route_day_num'] = (int)$this->params['route_day_num'];
        $this->params['cid'] = $this->params['cid'] ?? ''; //线路分类
        $this->params['day'] = $this->params['day'] ?? []; //行程

        $this->params['user_id'] = $this->userId;




        Log::error('创建线路参数: ', $this->params);


        if (!$this->params['route_name']) {
            throw new UnprocessableEntityHttpException(850030);
        }

        if (!$this->params['route_day_num']) {
            throw new UnprocessableEntityHttpException(850031);
        }
//        if (!$this->params['route_intro']) {
//            throw new UnprocessableEntityHttpException(850032);
//        }
//        if (!$this->params['cid']) {
//            throw new UnprocessableEntityHttpException(850033);
//        }
        if (true === empty($this->params['day'])) {
            throw new UnprocessableEntityHttpException(850034);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            $this->routeService->addRoute($this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('创建线路异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '创建成功']);
    }



    //编辑线路
    public function edit()
    {

        $this->params['route_id'] = $this->params['route_id'] ?? 0; //线路id
        $this->params['route_id'] = (int)$this->params['route_id'];
        $this->params['route_name'] = $this->params['route_name'] ?? ''; //线路名称
        $this->params['route_intro'] = $this->params['route_intro'] ?? ''; //线路介绍
        $this->params['route_day_num'] = $this->params['route_day_num'] ?? 0; //线路天数
        $this->params['route_day_num'] = (int)$this->params['route_day_num'];
        $this->params['cid'] = $this->params['cid'] ?? ''; //线路分类
        $this->params['day'] = $this->params['day'] ?? ''; //行程

        $this->params['user_id'] = (int)$this->userId;


        Log::error('编辑线路参数: ', $this->params);

        //检测线路是否存在
        $res = $this->routeService->checkRoute($this->params['route_id']);

        //检测线路是否是自己的
        if($this->params['user_id'] !== (int)$res['user_id']){
            throw new UnprocessableEntityHttpException(850038);
        }

        if (!$this->params['route_name']) {
            throw new UnprocessableEntityHttpException(850030);
        }

        if (!$this->params['route_day_num']) {
            throw new UnprocessableEntityHttpException(850031);
        }
//        if (!$this->params['route_intro']) {
//            throw new UnprocessableEntityHttpException(850032);
//        }
//        if (!$this->params['cid']) {
//            throw new UnprocessableEntityHttpException(850033);
//        }
        if (true === empty($this->params['day'])) {
            throw new UnprocessableEntityHttpException(850034);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            $this->routeService->editRoute($this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('编辑线路异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '编辑成功']);
    }


    //我的线路
    public function myRoute()
    {
        $this->params['user_id'] = $this->userId;
        $data = $this->routeService->getList($this->params);
        if (false === empty($data['data'])) {
            foreach ($data['data'] as $key => &$value) {

                //注意当前支付状态
                $value['pay_status'] = RouteDayRepository::getDayGoodsData($value['route_id'], $this->userId);
            }


        }
        return response_success($data);
    }
}
