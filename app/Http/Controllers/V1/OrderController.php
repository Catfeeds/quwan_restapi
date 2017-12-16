<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\Destination;
use App\Models\Holiday;
use App\Models\OrderCode;
use App\Models\RedStatus;
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
    const AUTO_CANCEL_TIME = 900; //自动取消订单时间 15分钟;

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
    protected $redStatus;

    public function __construct(
        RedStatus $redStatus,
        OrderService $orderService,
        HolidayService $holidayService,
        AttractionsService $attractionsService,
        TokenService $tokenService,
        Request $request,
        UserService $userService
    )
    {

        parent::__construct();

        $this->redStatus = $redStatus;
        $this->orderService = $orderService;
        $this->holidayService = $holidayService;
        $this->attractionsService = $attractionsService;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->request = $request;

        //接受到的参数
        $this->params = $this->request->all();

    }


    //订单退款
    public function orderRefund()
    {
        $this->params['order_id'] = $this->params['order_id'] ?? 0;//订单id
        $this->params['order_id'] = (int)$this->params['order_id'];

        if(!$this->params['order_id']){
            throw new UnprocessableEntityHttpException(850005);
        }

        Log::error('订单退款参数: ', $this->params);

        $userId = $this->userId;

        //获取订单信息
        $orderInfo = $this->orderService->getInfo($this->params['order_id']);
        if(true === empty($orderInfo)){
            throw new UnprocessableEntityHttpException(850004);
        }

        //检测是否是可以退款的订单
        if((int)$orderInfo['is_refund'] === \App\Models\Order::ORDER_IS_SCORE_0){
            throw new UnprocessableEntityHttpException(850054);
        }

        //检测是否可以退款
        if((int)$orderInfo['order_status'] === \App\Models\Order::ORDER_STATUS_10){
            throw new UnprocessableEntityHttpException(850049);
        }

        //检测是否已核销
        if((int)$orderInfo['order_status'] === \App\Models\Order::ORDER_STATUS_30){
            throw new UnprocessableEntityHttpException(850050);
        }

        //检测是否已取消或者已退过款
        if((int)$orderInfo['order_status'] === \App\Models\Order::ORDER_STATUS_0){
            if((int)$orderInfo['order_cancel_type'] > \App\Models\Order::ORDER_CANCEL_TYPE_2){
                throw new UnprocessableEntityHttpException(850051);
            }

            throw new UnprocessableEntityHttpException(850052);
        }


        //订单是否用户的
        if((int)$orderInfo['user_id'] !== $userId){
            throw new UnprocessableEntityHttpException(850000);
        }


        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->orderService->sendRefundo($orderInfo);
            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('订单退款异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }


        return ['msg' => '退款成功'];
    }


    //自动取消未支付订单
    public function autoOrderCancel()
    {
        //获取需要自动取消的订单
        $data = ['limit'=>100,'order_status'=>\App\Models\Order::ORDER_STATUS_10];
        $orderRes = $this->orderService->getCancelList($data);
        if(true === empty($orderRes)){
            return '无需要取消数据';
        }

        $arr = [];
        foreach ($orderRes as $key => $value) {
            if(time() > $value['order_created_at'] + self::AUTO_CANCEL_TIME){
                DB::connection('db_quwan')->beginTransaction();
                try {
                    $this->orderService->orderCance($value['order_id'], \App\Models\Order::ORDER_CANCEL_TYPE_2);
                    $arr[] = $value['order_id'];
                    Log::info('自动取消订单ok: '.$value['order_id']);
                    DB::connection('db_quwan')->commit();
                } catch (Exception $e) {
                    DB::connection('db_quwan')->rollBack();

                    //记错误日志
                    Log::error('自动取消订单异常: '.$value['order_id'], ['error' => $e]);
                    throw new UnprocessableEntityHttpException(850002);
                }
            }
        }

        return $arr;

    }

    //手动取消订单
    public function orderCancel()
    {
        $this->params['order_id'] = $this->params['order_id'] ?? 0;//订单id
        $this->params['order_id'] = (int)$this->params['order_id'];

        if(!$this->params['order_id']){
            throw new UnprocessableEntityHttpException(850005);
        }

        Log::error('手动取消订单参数: ', $this->params);

        $userId = $this->userId;

        //获取订单信息
        $orderInfo = $this->orderService->getInfo($this->params['order_id']);
        if(true === empty($orderInfo)){
            throw new UnprocessableEntityHttpException(850004);
        }

        //检测状态是否已是取消
        if((int)$orderInfo['order_status'] === \App\Models\Order::ORDER_STATUS_0){
            throw new UnprocessableEntityHttpException(850047);
        }

        //订单是否用户的
        if((int)$orderInfo['user_id'] !== $userId){
            throw new UnprocessableEntityHttpException(850000);
        }


        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->orderService->orderCance($this->params['order_id'], \App\Models\Order::ORDER_CANCEL_TYPE_1);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('手动取消订单异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }


        return ['msg' => '取消成功'];
    }


    //订单详情
    public function orderInfo()
    {

        $this->params['order_id'] = $this->params['order_id'] ?? 0;//订单id
        $this->params['order_id'] = (int)$this->params['order_id'];

        $this->params['user_id'] = (int)$this->userId;

        if(!$this->params['order_id']){
            throw new UnprocessableEntityHttpException(850005);
        }

        $data = $this->orderService->getListData($this->params);
        if(true === empty($data['data'][0])){
            throw new UnprocessableEntityHttpException(850004);
        }

        return $data['data'][0];
    }

    //订单列表
    public function orderList()
    {

        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];

        //订单状态(10未付款,20已支付，30已核销，40已评价，0已取消
        $this->params['order_status'] = $this->params['order_status'] ?? 0;
        $this->params['order_status'] = (int)$this->params['order_status'];
        $statusArr = [
            \App\Models\Order::ORDER_STATUS_10,
            \App\Models\Order::ORDER_STATUS_20,
            \App\Models\Order::ORDER_STATUS_30,
            \App\Models\Order::ORDER_STATUS_40,
            \App\Models\Order::ORDER_STATUS_0,
        ];
        if (!in_array($this->params['order_status'],$statusArr)) {
            throw new UnprocessableEntityHttpException(850005);
        }

        $this->params['user_id'] = (int)$this->userId;

        $data = $this->orderService->getListData($this->params);
        return response_success($data);
    }

    //红包设置
    public function hongbao(){
        //获取红包设置
        $res = $this->redStatus::getSet();
        return $res;
    }

    //支付回调通知
    public function notifyUrl()
    {
        Log::error('==========支付回调通知开始==================');

        $wxConfig = config('wx');
        $wxConfig['app_id'] = $wxConfig['xiao_app_id'];
        $wxConfig['secret'] = $wxConfig['xiao_secret'];
        $app = new Application($wxConfig);
        $response = $app->payment->handleNotify(function($notify, $successful){

            Log::error('支付回调参数: '. typeOf($notify));

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = $this->orderService->getInfoToSn($notify->out_trade_no);
            if (!$order) { // 如果订单不存在
                Log::error('订单不存在: '.$notify->out_trade_no);
                return 'SUCCESS';// 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            //`order_status` tinyint(1) NOT NULL DEFAULT '10' COMMENT '订单状态(10未付款,20已支付，30已核销，40已评价，0已取消',
            // 如果已支付,不在执行
            if((int)$order['order_status'] !== \App\Models\Order::ORDER_STATUS_10){
                Log::error('订单已支付过: '.$notify->out_trade_no);
                return 'SUCCESS';// 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // 用户是否支付成功
            if (!$successful) {
                Log::error('微信支付失败: '.$successful);
                return 'FAIL';
            }


            DB::connection('db_quwan')->beginTransaction();
            try {

                //是否主订单
                if($notify->attach){
                    Log::error('主订单: '.$notify->attach);
                    return 'SUCCESS';
                }else{

                    Log::error('子订单: '.$notify->out_trade_no);

                    // 修改订单状态,订单时间,第三方订单号,实际支付金额
                    $arr = [
                        'order_pay_amount' => $notify->total_fee / 100, //返回是分,要转换
                        'order_status' => \App\Models\Order::ORDER_STATUS_20,
                        'order_pay_at' => time(),
                        'transaction_id' => $notify->transaction_id,
                    ];
                    \App\Models\Order::where('order_id','=',$order['order_id'])->update($arr);

                    //生成兑换码(一个订单一个)
                    $codeArr = [
                        'shop_id' => $order['shop_id'],
                        'order_id' => $order['order_id'],
                        'code' => create_order_code(),
                        'created_at' => time(),
                    ];
                    OrderCode::create($codeArr);

                    //增加销售量(退款时候要减少)
                    if($order['order_type'] === \App\Models\Order::ORDER_TYPE_A){
                        //景点
                        Attractions::where('attractions_id','=',$order['join_id'])->increment('attractions_sales_num');
                    }elseif($order['order_type'] === \App\Models\Order::ORDER_TYPE_B){
                        //节日
                        Holiday::where('holiday_id','=',$order['join_id'])->increment('holiday_sales_num');
                    }
                }

                Log::error('修改订单状态成功: '.$notify->out_trade_no);

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

    //订单支付
    public function orderBuy()
    {
        $this->params['order_id'] = $this->params['order_id'] ?? 0;//订单id
        $this->params['order_id'] = (int)$this->params['order_id'];
        if(!$this->params['order_id']){
            throw new UnprocessableEntityHttpException(850005);
        }

        Log::error('订单支付参数: ', $this->params);

        //检测用户是否已绑定手机
        $userId = $this->userId;

        //获取订单信息
        $orderInfo = $this->orderService->getInfo($this->params['order_id']);
        if(true === empty($orderInfo)){
            throw new UnprocessableEntityHttpException(850004);
        }

        //检测状态是否已支付过
        if((int)$orderInfo['order_status'] === \App\Models\Order::ORDER_STATUS_20){
            throw new UnprocessableEntityHttpException(850048);
        }

        //订单是否用户的
        if((int)$orderInfo['user_id'] !== $userId){
            throw new UnprocessableEntityHttpException(850000);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {
            $orderInfo['order_sn'] = create_order_no();
            $data = $this->orderService->createWxOrder($orderInfo);

            //更新订单号
            \App\Models\Order::where('order_id','=',$orderInfo['order_id'])->update(['order_sn'=>$orderInfo['order_sn']]);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('订单支付异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }


        return $data;
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

        $this->params['order_sn'] = create_order_no();

        //下单
        $orderInfo = [
            'shop_id' => $goods['shop_id'],
            'order_sn' => $this->params['order_sn'],
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
