<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\FavService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class FavController
 * @package App\Http\Controllers\V1
 */
class FavController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $favService;

    public function __construct(TokenService $tokenService, Request $request,FavService $favService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->favService = $favService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function favList()
    {

        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];


        $this->params['fav_type'] = $this->params['fav_type'] ?? 0; //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $this->params['fav_type'] = (int)$this->params['fav_type'];

        $this->params['user_id'] = (int)$this->userId;

        if ($this->params['fav_type'] <= 0 || $this->params['fav_type'] >= 7) {
            throw new UnprocessableEntityHttpException(850005);
        }

        $data = $this->favService->getListData($this->params);
        return response_success($data);
    }

    public function add()
    {
        Log::error('收藏/取消开始====================');
        $this->params['user_id'] = $this->params['user_id'] ?? 0;
        $this->params['fav_type'] = $this->params['fav_type'] ?? 0; //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $this->params['join_id'] = $this->params['join_id'] ?? 0; 
        $this->params['user_id'] = (int)$this->params['user_id'];
        $this->params['fav_type'] = (int)$this->params['fav_type'];
        $this->params['join_id'] = (int)$this->params['join_id'];

        Log::error('参数:', $this->params);
        if (!$this->params['user_id'] || !$this->params['fav_type'] || !$this->params['join_id']) {
         throw new UnprocessableEntityHttpException(850005);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->favService->addOrDel($this->params);

            if($data === '收藏成功'){
                //记录收藏日志
                $logArr = [
                    'log_type' => \App\Models\Log::LOG_TYPE_4,
                    'log_time' => time(),
                    'user_id' => $this->userId,
                    'log_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ];
                \App\Models\Log::create($logArr);
            }


            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('收藏/取消异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        Log::error('收藏/取消结束====================');
        return ['msg'=>$data];
    }

}
