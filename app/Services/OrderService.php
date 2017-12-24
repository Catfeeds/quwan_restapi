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
use App\Models\Fav;
use App\Models\Holiday;
use App\Models\Order;
use App\Models\Img;
use App\Models\OrderCode;
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
    protected $favService;
    protected $fav;
    protected $orderCode;
    protected $holiday;

    public function __construct(
        Holiday $holiday,
        OrderCode $orderCode,
        Fav $fav,
        FavService $favService,
        Order $order,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->holiday = $holiday;
        $this->orderCode = $orderCode;
        $this->fav = $fav;
        $this->favService = $favService;
        $this->order = $order;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    //更新主订单信息
    public function editOriginal($userId,$originalId)
    {
        return $this->order::where('user_id','=',$userId)
            ->where('original_id','=',$originalId)
            ->update(['original_id'=>'','order_updated_at'=>time()]);
    }

    //企业支付
    public function sendMerchantPay($orderInfo,$amount)
    {
        $openid = User::where('user_id', '=', $orderInfo['user_id'])->value('openid');

        $wxConfig = config('wx');

        $wxConfig['app_id'] = $wxConfig['xiao_app_id'];
        $wxConfig['secret'] = $wxConfig['xiao_secret'];
        $app = new Application($wxConfig);

        $merchantPay = $app->merchant_pay;


        $merchantPayData = [
            'partner_trade_no' => $orderInfo['order_sn'], //商家订单号
            'openid' => $openid, //收款人的openid
            'check_name' => 'NO_CHECK',  //文档中有三种校验实名的方法 NO_CHECK OPTION_CHECK FORCE_CHECK
            //'re_user_name'=>'张三',     //OPTION_CHECK FORCE_CHECK 校验实名的时候必须提交
            'amount' => $amount*100,  //单位为分
            'desc' => '现金红包奖励',
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'] ?? '192.168.0.1',  //发起交易的IP地址
        ];
        Log::error('企业支付参数: ', $merchantPayData);
        $result = $merchantPay->send($merchantPayData);
        // 返回
        // 'return_code' => string 'SUCCESS' (length=7)
        // 'return_msg' => null
        // 'mchid' => string '1454303702' (length=10)
        // 'nonce_str' => string '5a36149c20566' (length=13)
        // 'result_code' => string 'SUCCESS' (length=7)
        // 'partner_trade_no' => string '20171217020358176381593944' (length=26)
        // 'payment_no' => string '1000018301201712172279848165' (length=28)
        // 'payment_time' => string '2017-12-17 14:54:21' (length=19)


        $result->return_code = $result->return_code ?? '';
        $result->result_code = $result->result_code ?? '';
        Log::error('企业支付返回: ', ['return_code' => $result->return_code, 'return_msg' => $result->return_msg]);
        if ($result->return_code !== 'SUCCESS' && $result->result_code !== 'SUCCESS') {
            throw new UnprocessableEntityHttpException(850057);
        }

        //更新红包奖励信息
        $arr = [
            'order_reward_amount' => $amount,
            'payment_no' => $result->payment_no,
            'order_updated_at' => time(),
        ];
        $this->order::where('order_id','=',$orderInfo['order_id'])->update($arr);


        return ['payment_no'=>$result->payment_no];

        //返回
//        'return_code' => string 'SUCCESS' (length=7)
//      'return_msg' => string 'OK' (length=2)
//      'appid' => string 'wxd87a756c26460edd' (length=18)
//      'mch_id' => string '1454303702' (length=10)
//      'nonce_str' => string 'koSk2QfWZQziZNhQ' (length=16)
//      'sign' => string '989381AF0315E3B540CB08DE8C1AF416' (length=32)
//      'result_code' => string 'SUCCESS' (length=7)
//      'transaction_id' => string '4200000027201712079741556061' (length=28)
//      'out_trade_no' => string '20171207091444074848198308' (length=26)
//      'out_refund_no' => string '20171207091444074848198308' (length=26)
//      'refund_id' => string '50000505022017121602652036942' (length=29)
//      'refund_channel' => null
//      'refund_fee' => string '10' (length=2)
//      'coupon_refund_fee' => string '0' (length=1)
//      'total_fee' => string '10' (length=2)
//      'cash_fee' => string '10' (length=2)
//      'coupon_refund_count' => string '0' (length=1)
//      'cash_refund_fee' => string '10' (length=2)




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

    //订单退款
    public function sendRefundo($orderInfo)
    {

        $wxConfig = config('wx');

        $wxConfig['app_id'] = $wxConfig['xiao_app_id'];
        $wxConfig['secret'] = $wxConfig['xiao_secret'];
        $app = new Application($wxConfig);
        $payment = $app->payment;

        $orderNo = $orderInfo['order_sn']; //商户订单号
        $refundNo = $orderInfo['order_sn']; //商户退款订单号

        //如果是主订单下的子订单退款
        if($orderInfo['original_id']){
            $orderNo = $orderInfo['original_id']; //商户订单号
            $refundNo = $orderInfo['order_sn']; //商户退款订单号
        }


        $totalFee = $orderInfo['order_pay_amount'] * 100; //订单金额
        $refundRee = $orderInfo['order_pay_amount'] * 100; //退款金额
        //$result = $payment->refund($orderNo, $orderNo, 100, 80, 1900000109); // 总金额 100， 退款 80，操作员：1900000109
        $result = $payment->refund($orderNo, $refundNo, $totalFee, $refundRee); // 总金额 100， 退款 80，操作员：1900000109

        $result->return_code = $result->return_code ?? '';
        $result->result_code = $result->result_code ?? '';
        Log::error('微信退款返回: ', ['return_code' => $result->return_code, 'return_msg' => $result->return_msg]);
        if ($result->return_code !== 'SUCCESS' && $result->result_code !== 'SUCCESS') {
            throw new UnprocessableEntityHttpException(850053);
        }

        //更新退款信息
        $arr = [
            'refund_id' => $result->refund_id,
            'order_refund_amount' => $result->refund_id,
            'order_status' => $this->order::ORDER_STATUS_0,
            'order_cancel_type' => $this->order::ORDER_CANCEL_TYPE_4,
            'order_refund_at' => time(),
        ];
        $this->order::where('order_id','=',$orderInfo['order_id'])->update($arr);


        //减少销售量
        if((int)$orderInfo['order_type'] === $this->order::ORDER_TYPE_A){
            //景点
            $goods = $this->attractions::getInfo($orderInfo['join_id']);

            if((int)$goods['attractions_sales_num'] < (int)$orderInfo['order_num']){
                $num = 0;
            }else{
                $num = $goods['attractions_sales_num'] - $orderInfo['order_num'];
            }

            $this->attractions::where('attractions_id','=',$orderInfo['join_id'])->update(['attractions_sales_num'=>$num]);

        }elseif((int)$orderInfo['order_type'] === $this->order::ORDER_TYPE_B){
            //节日
            $goods = $this->holiday::getInfoData($orderInfo['join_id']);

            if((int)$goods['holiday_sales_num'] < (int)$orderInfo['order_num']){
                $num = 0;
            }else{
                $num = $goods['holiday_sales_num'] - $orderInfo['order_num'];
            }

            $this->attractions::where('holiday_id','=',$orderInfo['join_id'])->update(['holiday_sales_num'=>$num]);
        }

        return ['refund_id'=>$result->refund_id];

        //返回
//        'return_code' => string 'SUCCESS' (length=7)
//      'return_msg' => string 'OK' (length=2)
//      'appid' => string 'wxd87a756c26460edd' (length=18)
//      'mch_id' => string '1454303702' (length=10)
//      'nonce_str' => string 'koSk2QfWZQziZNhQ' (length=16)
//      'sign' => string '989381AF0315E3B540CB08DE8C1AF416' (length=32)
//      'result_code' => string 'SUCCESS' (length=7)
//      'transaction_id' => string '4200000027201712079741556061' (length=28)
//      'out_trade_no' => string '20171207091444074848198308' (length=26)
//      'out_refund_no' => string '20171207091444074848198308' (length=26)
//      'refund_id' => string '50000505022017121602652036942' (length=29)
//      'refund_channel' => null
//      'refund_fee' => string '10' (length=2)
//      'coupon_refund_fee' => string '0' (length=1)
//      'total_fee' => string '10' (length=2)
//      'cash_fee' => string '10' (length=2)
//      'coupon_refund_count' => string '0' (length=1)
//      'cash_refund_fee' => string '10' (length=2)




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

    //获取需要自动取消的订单列表
    public function getCancelList($data)
    {
        $limit = $data['limit'] ?? 100; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;

        $query = $this->order::select('order_id','order_created_at');

        $wheres = [];
        $condition = array(array('column' => 'order_status', 'value' => $data['order_status'], 'operator' => '='));
        $wheres = array_merge($condition, $wheres);

        //载入查询条件
        $wheres = array_reverse($wheres);
        foreach ($wheres as $value) {
            $query->where($value['column'], $value['operator'], $value['value']);
        }

        $result = $query->skip($offset)->take($limit)->get()->toArray();

        return $result;

    }

    //修改订单状态
    public function orderCance($orderId,$orderCancelType)
    {
        $data = $this->order->orderCance($orderId,$orderCancelType);
        return $data;

    }


    //订单列表
    public function getListData($data)
    {
        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;


        $query = $this->order::select('*');

        $wheres = [];

        if (false === empty($data['user_id'])) {
            $condition = array(array('column' => 'user_id', 'value' => $data['user_id'], 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
        }

        if (false === empty($data['order_id'])) {
            $condition = array(array('column' => 'order_id', 'value' => $data['order_id'], 'operator' => '='));
            $wheres = array_merge($condition, $wheres);

        } else {
            //订单状态(10未付款,20已支付，30已核销，40已评价，0已取消
            $statusArr = [$this->order::ORDER_STATUS_10,$this->order::ORDER_STATUS_20,
                $this->order::ORDER_STATUS_30,$this->order::ORDER_STATUS_40,$this->order::ORDER_STATUS_0];
            if (in_array($data['order_status'], $statusArr)) {
                $condition = array(array('column' => 'order_status', 'value' => $data['order_status'], 'operator' => '='));
                $wheres = array_merge($condition, $wheres);
            }
        }



        //载入查询条件
        $wheres = array_reverse($wheres);
        foreach ($wheres as $value) {
            $query->where($value['column'], $value['operator'], $value['value']);
        }

        $result['_count'] = $query->count();
        $result['data'] = $query->skip($offset)->take($limit)->orderBy('order_id','DESC')->get()->toArray();
        if (false === empty($result['data'])) {
            foreach ($result['data'] as $key => &$value) {

                //商品名称图片
                $goodsInfo = $this->getGoodsInfo($value);
                $value['join_img'] = $goodsInfo['join_img'] ?? '';
                $value['join_name'] = $goodsInfo['join_name'] ?? '';
                $value['join_intro'] = $goodsInfo['join_intro'] ?? '';


                //倒计时订单取消时间 (15分钟) 注意计划任务取消订单
                $countdown = 900 - (time() - $value['order_created_at']);
                $countdown = $countdown > 0 ? $countdown : 0;
                $value['order_created_at'] = date('Y-m-d H:i:s',$value['order_created_at']);
                $value['order_cancel_countdown'] = $countdown;

                //兑换码
                $value['code'] = $this->orderCode::getOrderCode($value['order_id']);
            }
        }

        return $result;

    }


    //批量创建订单
    public function addAllOrder($params,$wxAmount,$userId,$originalId)
    {
        Log::error('批量创建订单参数: ', $params);

        //创建订单
        $goods = [];
        foreach ($params as $key => $value) {
            $orderRes = $this->order::create($value);
            if(!$orderRes){
                throw new UnprocessableEntityHttpException(850043);
            }
            $goods[] = [
                'join_type' => $value['order_type'],
                'join_id' => $value['join_id'],
                'order_sn' => $value['order_sn'],
            ];
        }



        // $orderRes = $this->order::insert($params);
        // if(!$orderRes){
        //     throw new UnprocessableEntityHttpException(850043);
        // }



        //创建微信订单
        $arr = [
            'user_id' => $userId,
            'order_amount' => $wxAmount,
            'order_sn' => $originalId,
            'original_id' => $originalId,
        ];
        $res =  $this->createWxOrder($arr);
        $res['order_sn'] = $goods;
        // var_dump($goods,$res);die;
        return $res;
    }

    //创建订单
    public function addOrder($params)
    {
        Log::error('创建订单参数: ', $params);
        //创建订单
        $orderRes = $this->order::create($params);
        if(!$orderRes){
            throw new UnprocessableEntityHttpException(850043);
        }

        //创建微信订单
        return $this->createWxOrder($params);
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

    /**
     * 获取订单商品信息
     * @param $value
     * @return array
     */
    public function getGoodsInfo($value)
    {
        //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $res = [];
        switch ((int)$value['order_type']) {
            case $this->fav::FAV_TYPE_A: //景点
                $info = $this->favService->getAttractionData($value['join_id']);
                break;
            case $this->fav::FAV_TYPE_B: //节日
                $info = $this->favService->getHolidayData($value['join_id']);
                break;
            case $this->fav::FAV_TYPE_C: //酒店
                $info = $this->favService->getHotelData($value['join_id']);
                break;
            case $this->fav::FAV_TYPE_D: //餐厅
                $info = $this->favService->getHallData($value['join_id']);
                break;
            default: $info = [];break;
        }

        if (false === empty($info)) {

            //@todo 注意图片url处理
            $res['join_img'] = $info['img'][0] ?? '';
            $res['join_name'] = $info['name'];
            $res['join_intro'] = $info['intro'];
        }

        return $res;
    }

    /**
     * 创建微信订单
     * @param $params
     * @return array
     */
    public function createWxOrder($params)
    {
        $openid = User::where('user_id', '=', $params['user_id'])->value('openid');
        //$openid = 'oal4F0bkh9UTjvGaEEC21M5hv_cM';

        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => '商品支付',
            //'detail'           => 'iPad mini 16G 白色',
            'out_trade_no' => $params['order_sn'],
            'total_fee' => $params['order_amount'] * 100, // 单位：分
            'openid' => $openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            'attach' => $params['original_id'] ?? '', // original_id
        ];

        Log::error('创建微信订单参数: ', $attributes);
        $order = new \EasyWeChat\Payment\Order($attributes);

        $wxConfig = config('wx');

        $wxConfig['app_id'] = $wxConfig['xiao_app_id'];
        $wxConfig['secret'] = $wxConfig['xiao_secret'];
        $app = new Application($wxConfig);
        $payment = $app->payment;

        $result = $payment->prepare($order);

        //如果有返回预支付订单,,记录起来
//        if (false === $result->prepay_id) {
//            $this->order::where('order_id', '=', $orderRes->id)->update(['prepay_id' => $result->prepay_id]);
//        }

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
        if ($result->return_code !== 'SUCCESS' && $result->result_code !== 'SUCCESS') {
            throw new UnprocessableEntityHttpException(850044);
        }


        return [
            'order_sn' => $params['order_sn'],
            'prepay_id' => $result->prepay_id,
            'original_id' => $params['original_id'] ?? '',
        ];
    }
}