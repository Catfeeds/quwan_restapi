<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Destination;
use EasyWeChat\Foundation\Application;
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
        ];
        $order = new Order($attributes);

        return $order;
    }

    public function sendHongBao()
    {

        $wxConfig = config('wx');
        $app = new Application($wxConfig);
        $luckyMoney = $app->lucky_money;

        $luckyMoneyData = [
            'mch_billno'       => 'xy123456789',
            'send_name'        => '开发测试发红包',
            're_openid'        => 'oal4F0bkh9UTjvGaEEC21M5hv_cM',
            'total_num'        => 1,  //固定为1，可不传
            'total_amount'     => 100,  //单位为分，不小于100
            'wishing'          => '祝福语',
            'client_ip'        => '192.168.0.1',  //可不传，不传则由 SDK 取当前客户端 IP
            'act_name'         => '测试活动',
            'remark'           => '测试备注',
        ];
        $result = $luckyMoney->sendNormal($luckyMoneyData);

        //$mchBillNo = "xy123456";
        //$result = $luckyMoney->query($mchBillNo);
        return ['luckyMoneyData'=>$luckyMoneyData,'result'=>$result];

    }



    public function sendMerchantPay()
    {
        $wxConfig = config('wx');
        $app = new Application($wxConfig);
        $merchantPay = $app->merchant_pay;

        $merchantPayData = [
            'partner_trade_no' => str_random(16), //随机字符串作为订单号，跟红包和支付一个概念。
            'openid' => 'oal4F0bkh9UTjvGaEEC21M5hv_cM', //收款人的openid
            'check_name' => 'NO_CHECK',  //文档中有三种校验实名的方法 NO_CHECK OPTION_CHECK FORCE_CHECK
            're_user_name'=>'张三',     //OPTION_CHECK FORCE_CHECK 校验实名的时候必须提交
            'amount' => 100,  //单位为分
            'desc' => '开发测试企业付款',
            'spbill_create_ip' => '192.168.0.1',  //发起交易的IP地址
        ];
        //var_dump($merchantPayData);
        $result = $merchantPay->send($merchantPayData);
        return ['merchantPayData'=>$merchantPayData,'result'=>$result];
        //$partnerTradeNo = "商户系统内部的订单号（partner_trade_no）";
        //$merchantPay->query($partnerTradeNo);
    }

    public function sendRefundo()
    {

        $wxConfig = config('wx');
        $app = new Application($wxConfig);
        $payment = $app->payment;;

        $orderNo = str_random(16);
        $refundNo = str_random(16);
        $result = $payment->refund($orderNo, $refundNo, 100, 80, 1900000109); // 总金额 100， 退款 80，操作员：1900000109

        return ['orderNo'=>$orderNo,'refundNo'=>$refundNo,'result'=>$result];

//        $luckyMoney = $app->lucky_money;
//
//        $luckyMoneyData = [
//            'mch_billno'       => 'xy123456',
//            'send_name'        => '开发测试发红包',
//            're_openid'        => 'ovwAZuBLwSiize3Zjd-DiCZPWTf8',
//            'total_num'        => 1,  //固定为1，可不传
//            'total_amount'     => 100,  //单位为分，不小于100
//            'wishing'          => '祝福语',
//            'client_ip'        => '192.168.0.1',  //可不传，不传则由 SDK 取当前客户端 IP
//            'act_name'         => '测试活动',
//            'remark'           => '测试备注',
//        ];
//        //$result = $luckyMoney->sendNormal($luckyMoneyData);
//
//        $mchBillNo = "xy123456";
//        $result = $luckyMoney->query($mchBillNo);
//        return $result;

    }




}
