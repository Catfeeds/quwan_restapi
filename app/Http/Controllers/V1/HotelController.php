<?php

namespace App\Http\Controllers\V1;


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

    public function __construct(TokenService $tokenService, Request $request,HotelService $hotelService)
    {

        parent::__construct();

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
        return response_success($data);
    }

}
