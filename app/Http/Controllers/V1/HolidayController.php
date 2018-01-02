<?php

namespace App\Http\Controllers\V1;


use App\Models\Fav;
use App\Models\Order;
use App\Services\FavService;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class HolidayController
 * @package App\Http\Controllers\V1
 */
class HolidayController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $holidayService;
    protected $favService;

    public function __construct(FavService $favService,TokenService $tokenService, Request $request,HolidayService $holidayService)
    {

        parent::__construct();

        $this->favService = $favService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->holidayService = $holidayService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index($holiday_id = 0)
    {
        $holidayId = $holiday_id ?? 0;
        $data = $this->holidayService->getData($holidayId);
        $data['code'] = [];
        $data['is_fav'] = 0;

        $userId = $this->userId;
        if($userId){
            //关联的订单兑换码
            $data['code'] = Order::getTypeCode($userId,Order::ORDER_TYPE_B,$holidayId);

            //检测用户是否收藏 1景点,2目的地，3路线,4节日，5酒店,6餐厅
            $data['is_fav'] = $this->favService->isFav(Fav::FAV_TYPE_B, $userId, $holidayId) ? 1 : 0;
        }

        $data['holiday_start_at'] = date('Y年m月d日', $data['holiday_start_at']);
        $data['holiday_end_at']   = date('Y年m月d日', $data['holiday_end_at']);

        return response_success($data);
    }

}
