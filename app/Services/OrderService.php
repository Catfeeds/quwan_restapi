<?php
/**
 * Created by PhpStorm.
 * Order: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Order;
use App\Models\Img;
use App\Models\Route;
use App\Models\User;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $order;

    public function __construct(
        Order $order,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->order = $order;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }


    //获取用户订单统计信息
    public function addOrder($params)
    {
        Log::error('创建订单参数: ', $params);
        //创建订单
        $orderRes = $this->order::create($params);
        if(!$orderRes){
            throw new UnprocessableEntityHttpException(850043);
        }

        $openid = User::where('user_id','=',$params['user_id'])->value('openid');
        //$openid = 'oal4F0bkh9UTjvGaEEC21M5hv_cM';

        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => '商品支付',
            //'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => $params['order_sn'],
            'total_fee'        => $params['order_amount'] * 100, // 单位：分
            'openid'           => $openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            'attach'           => '主订单号:', // original_id
        ];

        Log::error('创建微信订单参数: ', $attributes);
        $order = new \EasyWeChat\Payment\Order($attributes);

        $wxConfig = config('wx');

        $wxConfig['app_id'] = $wxConfig['xiao_app_id'];
        $wxConfig['secret'] = $wxConfig['xiao_secret'];
        $app = new Application($wxConfig);
        $payment = $app->payment;

        $result = $payment->prepare($order);

//        'return_code' => string 'SUCCESS' (length=7)
//      'return_msg' => string 'OK' (length=2)
//      'appid' => string 'wxb74f749dec2016f6' (length=18)
//      'mch_id' => string '1454303702' (length=10)
//      'nonce_str' => string 'NQfoL4k8Jp0gn0gs' (length=16)
//      'sign' => string 'D28B49F2D0AADB49A3E86C6846EFE214' (length=32)
//      'result_code' => string 'SUCCESS' (length=7)
//      'prepay_id' => string 'wx201712032015448522600ea00582949581' (length=36)
//      'trade_type' => string 'JSAPI' (length=5)

        $result->return_code = $result->return_code ?? '';
        $result->result_code = $result->result_code ?? '';
        Log::error('微信返回: ', ['return_code' => $result->return_code, 'return_msg' => $result->return_msg]);
        if ($result->return_code !== 'SUCCESS' && $result->result_code !=='SUCCESS'){
            throw new UnprocessableEntityHttpException(850044);
        }

        return ['prepay_id' => $result->prepay_id];
    }


    //获取用户订单统计信息
    public function getCountInfo($orderId)
    {
        $data = $this->order->countInfo($orderId);
        return $data;

    }



    //通过订单id获取订单信息
    public function getInfo($orderId)
    {
        $data = $this->order->getInfo($orderId);
        return $data;

    }

    //通过订单sn获取订单信息
    public function getInfoToSn($orderSn)
    {
        $data = $this->order->getInfoToSn($orderSn);
        return $data;

    }
}