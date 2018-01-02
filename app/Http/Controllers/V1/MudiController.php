<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Fav;
use App\Services\MudiService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class HomeController
 * @package App\Http\Controllers\V1
 */
class MudiController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;
    protected $params;
    protected $destinationJoin;
    protected $mudiService;

    public function __construct(TokenService $tokenService, Request $request, MudiService $mudiService,DestinationJoin $destinationJoin)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->destinationJoin = $destinationJoin;
        $this->mudiService = $mudiService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //目的地详情页数据
    public function index($destination_id = 0)
    {
        $destinationId = $destination_id ?? 0;
        $data = $this->mudiService->getData($destinationId);

        return response_success($data);
    }

}
