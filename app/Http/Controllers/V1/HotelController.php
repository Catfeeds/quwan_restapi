<?php

namespace App\Http\Controllers\V1;


use App\Models\Fav;
use App\Services\FavService;
use App\Services\HotelService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class HotelController
 * @package App\Http\Controllers\V1
 */
class HotelController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $hotelService;
    protected $favService;

    public function __construct(FavService $favService,TokenService $tokenService, Request $request,HotelService $hotelService)
    {

        parent::__construct();

        $this->favService = $favService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->hotelService = $hotelService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index($hotel_id = 0)
    {
        $hotelId = $hotel_id ?? 0;
        $data = $this->hotelService->getData($hotelId);

        $data['is_fav'] = 0;

        $userId = $this->userId;
        if($userId){
            //检测用户是否收藏 1景点,2目的地，3路线,4节日，5酒店,6餐厅
            $data['is_fav'] = $this->favService->isFav(Fav::FAV_TYPE_C, $userId, $hotelId) ? 1 : 0;
        }

        return response_success($data);
    }

}
