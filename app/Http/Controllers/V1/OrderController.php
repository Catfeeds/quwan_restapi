<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Destination;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class OrderController
 * @package App\Http\Controllers\V1
 */
class OrderController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;
    protected $params;

    public function __construct(TokenService $tokenService, Request $request)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function addOrder()
    {
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => 'iPad mini 16G 白色',
            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => '1217752501201407033233368018',
            'total_fee'        => 5388, // 单位：分
            'openid'           => 'ovwAZuBLwSiize3Zjd-DiCZPWTf8', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new Order($attributes);


        return response_success($order);
    }

}
