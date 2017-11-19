<?php

namespace App\Http\Controllers\V1;


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

    public function __construct(TokenService $tokenService, Request $request,HallService $hallService)
    {

        parent::__construct();

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
        return response_success($data);
    }

}
