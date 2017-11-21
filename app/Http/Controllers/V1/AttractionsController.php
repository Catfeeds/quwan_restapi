<?php

namespace App\Http\Controllers\V1;


use App\Services\AttractionsService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class AttractionsController
 * @package App\Http\Controllers\V1
 */
class AttractionsController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $attractionsService;

    public function __construct(TokenService $tokenService, Request $request,AttractionsService $attractionsService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->attractionsService = $attractionsService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index($attractions_id = 0)
    {
        $attractionsId = $attractions_id ?? 0;
        $data = $this->attractionsService->getData($attractionsId);
        return response_success($data);
    }

}
