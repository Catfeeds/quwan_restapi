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

    public function index()
    {
        $this->params['user_id'] = $this->params['user_id'] ?? 0;
        $this->params['fav_type'] = $this->params['fav_type'] ?? 0; //1景点,2节日，3酒店,4餐厅
        $this->params['join_id'] = $this->params['join_id'] ?? 0; 
        $this->params['user_id'] = (int)$this->params['user_id'];
        $this->params['fav_type'] = (int)$this->params['fav_type'];
        $this->params['join_id'] = (int)$this->params['join_id'];
        
        if (!$this->params['user_id'] || !$this->params['fav_type'] || !$this->params['join_id']) {
         throw new UnprocessableEntityHttpException(850005);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->favService->addOrDel($this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('收藏/取消异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }
        return response_success(['msg'=>'操作成功']);
    }

}
