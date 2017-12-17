<?php

namespace App\Http\Controllers\V1;


use App\Models\CidMap;
use App\Models\Order;
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
    protected $cidMap;

    public function __construct(CidMap $cidMap,TokenService $tokenService, Request $request,AttractionsService $attractionsService)
    {

        parent::__construct();

        $this->cidMap = $cidMap;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->attractionsService = $attractionsService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //景点分类列表
    public function cid()
    {
        $data = $this->cidMap->getTypeCidLists($this->cidMap::CID_MAP_TYPE_1);

        return $data;
    }

    public function index($attractions_id = 0)
    {
        $attractionsId = $attractions_id ?? 0;
        $data = $this->attractionsService->getData($attractionsId);
        $data['code'] = [];

        $userId = $this->userId;
        if($userId){
            //关联的订单兑换码
            $data['code'] = Order::getTypeCode($userId,Order::ORDER_TYPE_A,$attractions_id);
        }

        return response_success($data);
    }

}
