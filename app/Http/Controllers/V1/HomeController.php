<?php

namespace App\Http\Controllers\V1;


use App\Models\HomePage;
use App\Repository\HomePageRepository;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;

/**
 * Class HomeController
 * @package App\Http\Controllers\V1
 */
class HomeController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;
    protected $params;
    protected $homePage;
    protected $homePageRepository;

    public function __construct(TokenService $tokenService, Request $request,HomePage $homePage,HomePageRepository $homePageRepository)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->homePage = $homePage;
        $this->homePageRepository = $homePageRepository;

        //接受到的参数
        $this->params = $this->request->all();

    }



    //wx
    public function wx()
    {
        $wxConfig = config('wx');


        $app = new Application($wxConfig);
//        $oauth = $app->oauth;
//
//        // 获取 OAuth 授权结果用户信息
//        $user = $oauth->user();
//        $userArr = $user->toArray();
//        Log::error('登录用户: ', $userArr);
//
//        $targetUrl = empty($userArr['target_url']) ? '/' : $userArr['target_url'];
//        header('location:'. $targetUrl); // 跳转到 user/profile


        $oauth = $app->oauth;
        // 未登录
//        if (empty($_SESSION['wechat_user'])) {
//            $_SESSION['target_url'] = 'user/profile';
            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            // $oauth->redirect()->send();
//        }
        // 已经登录过
//        $user = $_SESSION['wechat_user'];


    }


    public function index()
    {
        //获取首页数据
        $data = $this->homePage->getHomeData();
        $data = $this->homePageRepository->getPageData($data);

        return response_success($data);
    }




        public function addData()
    {
        //获取首页数据


        //return response_success(['data' => $data);

        //增加分类数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//                'cid_name' => '餐厅分类' . $i,
//                'cid_type' => 6,
//                'cid_sort' => $i,
//            ];
//        }
//        Cid::insert($data);


//        //增加景点数据
//        $data = [];
//        for ($i = 0; $i < 20; $i++) {
//            $data[] = [
//                'attractions_name' => '景点名称'.$i,
//                'attractions_address' => '地址'.$i,
//                'attractions_phone' => '0571-4564897'.$i,
//                'attractions_price' => '25'.$i.'.89',
//                'attractions_intro' => '介绍'.$i,
//                'attractions_score' => '82.3'.$i,
//                'attractions_evaluation' => '8.9'.$i,
//                'attractions_lon' => '3'.$i.'.546566',
//                'attractions_lat' => '10'.$i.'.075546',
//                'attractions_start_at' => time()-$i,
//                'attractions_end_at' => time()+10000+$i,
//                'attractions_sort' => $i,
//                'attractions_created_at' => time(),
//            ];
//        }
//        Attractions::insert($data);


//        //增加分类引用数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//                'cid_id' => $i+1,
//                'join_id' => $i+1,
//                'cid_map_sort' => $i+1,
//                'cid_map_type' => 1,
//            ];
//        }
//        CidMap::insert($data);

//        //增加目的地数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//                'destination_name' => '目的地名称'.$i,
//                'destination_status' => '0禁用,1启用',
//                'destination_created_at' => time(),
//                'destination_updated_at' => time(),
//            ];
//        }
//        Destination::insert($data);


//        //增加目的地数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                'destination_id' => $i+1,
//                'join_id' => $i+1,
//                'destination_join_sort' => $i+1,
//                'destination_join_type' => 4,
//            ];
//        }
//        DestinationJoin::insert($data);

//        //增加收藏数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                'join_id' => $i + 1,
//                'fav_type' => 4,
//                'user_id' => $i + 1,
//                'fav_created_at' => time(),
//                'fav_updated_at' => time(),
//            ];
//        }
//        Fav::insert($data);

//        //增加业务图片数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                //'1景点,2节日，3酒店,4餐厅,5评价',
//                'join_id' => $i+1,
//                'img_sort' => $i+1,
//                'img_type' => 5,
//                'img_url' => 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=1140985205,3685509106&fm=58&u_exp_0=573951590,3352203696&fm_exp_0=86&bpow=650&bpoh=650',
//                'img_created_at' => time(),
//                'img_updated_at' => time(),
//            ];
//        }
//        Img::insert($data);

        //增加站内消息数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                'user_id'=> $i+1,
//                'message_type' => 2,
//                'message_comment' =>'内容'.$i+1,
//                'message_created_at' => time(),
//                'message_updated_at' => time(),
//            ];
//        }
//        Message::insert($data);

        //增加线路数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                'route_name' => '线路名称'.$i+1,
//                'route_day_num' => $i+1,
//                'route_created_at' => time(),
//                'route_updated_at' => time(),
//            ];
//        }
//        Route::insert($data);

//        //增加线路数据
//        $data = [];
//        for ($i = 0; $i < 20; $i++) {
//            $data[] = [
//
//                'route_id'  => $i+1,
//                'route_day_intro' => '日程介绍'.$i+1,
//                'route_day_sort' => $i+1,
//            ];
//        }
//        RouteDay::insert($data);

//        //增加线路数据
//        $data = [];
//        for ($i = 0; $i < 20; $i++) {
//            $data[] = [
//
//                'route_day_id' => $i+1,
//                'join_id' => $i+1,
//                'route_day_join_sort' => $i+1,
//                'route_day_join_type' => 5
//            ];
//        }
//        RouteDayJoin::insert($data);


//        //增加线路数据
//        $data = [];
//        for ($i = 0; $i < 20; $i++) {
//            $data[] = [
//                'user_id' => $i + 1,
//                'join_id' => $i + 1,
//                'order_id' => $i + 1,
//                'score' => '3' . $i . '4',
//                'score_comment' => '内容' . $i,
//                'score_type' => 4,
//                'score_created_at' => time(),
//                'score_updated_at' => time(),
//            ];
//        }
//        Score::insert($data);

//        //增加线路数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//                'user_nickname' => '用户昵称'.$i + 1,
//                'user_avatar' => 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=547138142,3998729701&fm=27&gp=0.jpg',
//                'user_mobile' => '135888888'.$i + 1,
//                'user_is_binding' => 1,
//                'openid' => '微信openid'.$i + 1,
//                'user_lon' => '2'.$i.'.546566',
//                'user_lat' => '1'.$i.'.075546',
//                'user_created_at' =>  time(),
//                'user_updated_at' =>  time(),
//            ];
//        }
//        User::insert($data);
//



//                'attractions_lon' => '3'.$i.'.546566',
//                'attractions_lat' => '10'.$i.'.075546',


//
//        //增加业务图片数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//
//                'order_id' => $i+1,
//                'code' => uuid(),
//                'created_at' => time(),
//            ];
//        }
//        OrderCode::insert($data);


//
//
//        //增加业务图片数据
//        $data = [];
//        for ($i = 0; $i < 10; $i++) {
//            $data[] = [
//
//                'order_sn' =>build_order_no(),
//                'join_id' => $i+1,
//                'order_type' => 1,
//                'order_num' => $i+1,
//                'order_price' => '25'.$i.'.89',
//                'order_amount' => '25'.$i.'.89'*($i+1),
//                'order_pay_amount' => '25'.$i.'.89'*($i+1),
//                //  'order_refund_amount' => '退款金额',
//                //  'order_reward_amount' => '奖励的红包金额',
//
//                //  'order_is_score' => 1,
//                'order_status' => 0,
//                'user_id' =>  $i+1,
//                //  'order_pay_at' => time(),
//                //  'order_refund_at' => '退款时间',
//                //  'order_cancel_at' => '取消时间',
//                //  'order_verify_at' => time(),
//                'order_created_at' => time(),
//                'order_updated_at' => time(),
//            ];
//        }
//        Order::insert($data);


    }

}
