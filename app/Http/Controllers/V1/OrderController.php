<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\Destination;
use App\Models\Holiday;
use App\Models\OrderCode;
use App\Services\AttractionsService;
use App\Services\HolidayService;
use App\Services\OrderService;
use App\Services\UserService;
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
    protected $userService;
    protected $attractionsService;
    protected $holidayService;
    protected $orderService;

    public function __construct(
        OrderService $orderService,
        HolidayService $holidayService,
        AttractionsService $attractionsService,
        TokenService $tokenService,
        Request $request,
        UserService $userService
    )
    {

        parent::__construct();

        $this->orderService = $orderService;
        $this->holidayService = $holidayService;
        $this->attractionsService = $attractionsService;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->request = $request;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //支付回调通知
    public function notifyUrl()
    {
        Log::error('支付回调参数: ', $this->params);

        $wxConfig = config('wx');
        $app = new Application($wxConfig);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = $this->orderService->getInfoToSn($notify->out_trade_no);
            if (!$order) { // 如果订单不存在
                return 'SUCCESS';// 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            //`order_status` tinyint(1) NOT NULL DEFAULT '10' COMMENT '订单状态(10未付款,20已支付，30已核销，40已评价，0已取消',
            // 如果已支付,不在执行
            if((int)$order['order_status'] !== \App\Models\Order::ORDER_STATUS_10){
                return 'SUCCESS';// 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // 用户是否支付成功
            if ($successful) {
                return 'FAIL';
            }


            DB::connection('db_quwan')->beginTransaction();
            try {

                //是否主订单
                if($notify->attach){
                    return 'SUCCESS';
                }else{

                    // 修改订单状态,订单时间,第三方订单号,实际支付金额
                    $arr = [
                        'order_pay_amount' => $notify->total_fee / 100, //返回是分,要转换
                        'order_status' => \App\Models\Order::ORDER_STATUS_20,
                        'order_pay_at' => time(),
                        'transaction_id' => $notify->transaction_id,
                    ];
                    Order::where('order_id','=',$order['order_id'])->update($arr);

                    //@todo 增加销售量
                    //if($order['order_type'] === \App\Models\Order::ORDER_TYPE_A){
                        //景点


                    //}elseif($order['order_type'] === \App\Models\Order::ORDER_TYPE_B){
                        //节日

                    //}
                }

                DB::connection('db_quwan')->commit();

            } catch (Exception $e) {
                DB::connection('db_quwan')->rollBack();
                //记错误日志
                Log::error('修改订单状态异常: ', ['error' => $e]);
                return 'FAIL';
            }

            return 'SUCCESS'; // 返回处理完成
        });

        return $response;
    }

    //购买 [景点,节日]
    public function buy()
    {
        $this->params['join_id'] = $this->params['join_id'] ?? 0;//商品id
        $this->params['join_id'] = (int)$this->params['join_id'];

        $this->params['order_type'] = $this->params['order_type'] ?? 0; //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $this->params['order_type'] = (int)$this->params['order_type'];

        $this->params['order_num'] = $this->params['order_num'] ?? 0; //订单数量
        $this->params['order_num'] = (int)$this->params['order_num'];

        Log::error('购买参数: ', $this->params);

        //检测用户是否已绑定手机
        $userId = $this->userId;
        $this->userService->checkBindMobile($userId);

        //订单类型
        $arr = [\App\Models\Order::ORDER_TYPE_A,\App\Models\Order::ORDER_TYPE_B];
        if (!in_array($this->params['order_type'], $arr)) {
            throw new UnprocessableEntityHttpException(850041);
        }

        //商品是否下架
        if($this->params['order_type'] === \App\Models\Order::ORDER_TYPE_A){
            //景点
            $goods = $this->attractionsService->getData($this->params['join_id']);

            if((int)$goods['attractions_status'] !== Attractions::ATTRACTIONS_STATUS_1){
                throw new UnprocessableEntityHttpException(850040);
            }

            $orderPrice = $goods['attractions_price'];
            $orderAmount = $goods['attractions_price'] * $this->params['order_num'];

        }elseif($this->params['order_type'] === \App\Models\Order::ORDER_TYPE_B){
            //节日
            $goods = $this->holidayService->getData($this->params['join_id']);

            if((int)$goods['holiday_status'] !== Holiday::HOLIDAY_STATUS_1){
                throw new UnprocessableEntityHttpException(850040);
            }

            $orderPrice = $goods['holiday_price'];
            $orderAmount = $goods['holiday_price'] * $this->params['order_num'];
        }

        //件数
        if (!$this->params['order_num']) {
            throw new UnprocessableEntityHttpException(850042);
        }

        //下单
        $orderInfo = [
           'shop_id' => $goods['shop_id'],
           'order_sn' => create_order_no(),
           'join_id' => $this->params['join_id'],
           'order_type' => $this->params['order_type'],
           'order_num' => $this->params['order_num'],
           'order_price' => $orderPrice,
           'order_amount' => $orderAmount,
           'user_id' => $userId,
           'order_created_at' => time(),
        ];

        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->orderService->addOrder($orderInfo);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('购买异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }


        return $data;
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

    //获取用户订单兑换码
    public static function getTypeCode($userId,$orderType,$joinId)
    {
        $code = self::select('c.order_id','c.code','c.is_exchange')
                ->leftJoin('order_code as c', 'c.order_id', '=', 'order.id')
                ->where('order.user_id', '=', $userId)
                ->where('order.order_type', '=', $orderType)
                ->where('order.join_id', '=', $joinId)
                ->where('c.is_exchange', '=', OrderCode::IS_EXCHANGE_0)
                ->get()
                ->toArray();
        return $code;
    }

}
