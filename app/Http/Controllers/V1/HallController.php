<?php

namespace App\Http\Controllers\V1;


use App\Models\Fav;
use App\Services\FavService;
use App\Services\HallService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class HallController
 * @package App\Http\Controllers\V1
 */
class HallController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $hallService;
    protected $favService;

    public function __construct(FavService $favService,TokenService $tokenService, Request $request,HallService $hallService)
    {

        parent::__construct();

        $this->favService = $favService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->hallService = $hallService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index($hall_id = 0)
    {
        $hallId = $hall_id ?? 0;
        $data = $this->hallService->getData($hallId);

        $data['is_fav'] = 0;

        $userId = $this->userId;
        if($userId){
            //检测用户是否收藏 1景点,2目的地，3路线,4节日，5酒店,6餐厅
            $data['is_fav'] = $this->favService->isFav(Fav::FAV_TYPE_D, $userId, $hallId) ? 1 : 0;
        }

        return response_success($data);
    }

}
