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

    public function __construct(
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


    //获取需啊哟自动取消的订单列表
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
            'attach'           => '', // original_id
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
        if(false === $result->prepay_id){
            $this->order::where('order_id','=',$orderRes->id)->update(['prepay_id'=>$result->prepay_id]);
        }

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

        return [
            'order_sn' => $params['order_sn'],
            'prepay_id' => $result->prepay_id,
            ];
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
        }

        return $res;
    }
}