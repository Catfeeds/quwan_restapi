<?php

namespace App\Http\Controllers\V1;


use App\Models\Order;
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

    public function __construct(TokenService $tokenService, Request $request,HolidayService $holidayService)
    {

        parent::__construct();

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

        $userId = $this->userId;
        if($userId){
            //关联的订单兑换码
            $data['code'] = Order::getTypeCode($userId,Order::ORDER_TYPE_B,$holiday_id);
        }
        return response_success($data);
    }

}
