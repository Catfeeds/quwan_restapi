<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Cid;
use App\Services\SmsService;
use App\Services\XSService;
use App\Services\YanzhenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;

/**
 * Class XSController
 * @package App\Http\Controllers\V1
 */
class XSController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $smsService;
    protected $XSService;

    public function __construct(
        XSService $XSService,
        TokenService $tokenService,
        Request $request,
        YanzhenService $yanzhenService
    )
    {

        parent::__construct();
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->yanzhenService = $yanzhenService;
        $this->XSService = $XSService;

        //接受到的参数
        $this->params = $this->request->all();

    }


    //修改文档
    public function editIndex()
    {
        $params['type'] = $this->params['type'] ?? 0;   //类型 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $params['type'] = (int) $params['type'];
        $params['id'] = $this->params['id'] ?? 0;       //主键
        $params['id'] = (int) $params['id'];
        $params['name'] = $this->params['name'] ?? '';  //名称
        $params['address'] = $this->params['address'] ?? ''; //地址
        $params['img'] = $this->params['img'] ?? '';        //图片
        $params['phone'] = $this->params['phone'] ?? '';    //电话
        $params['price'] = $this->params['price'] ?? 0.00;  //价格
        $params['price'] = (float) $params['price'];
        $params['intro'] = $this->params['intro'] ?? '';    //简介
        $params['score'] = $this->params['score'] ?? 0.00;  //评分
        $params['score'] = (float) $params['score'];
        $params['evaluation'] = $this->params['evaluation'] ?? 0; //评价
        $params['evaluation'] = (int) $params['evaluation'];
        $params['lon'] = $this->params['lon'] ?? 0.00;      //经度
        $params['lon'] = (float) $params['lon'];
        $params['lat'] = $this->params['lat'] ?? 0.00;      //纬度
        $params['lat'] = (float) $params['lat'];
        $params['geohash'] = $this->params['geohash'] ?? '';        //经纬度换算的字符串
        $params['open_time'] = $this->params['open_time'] ?? '';    //开放时间
        $params['sort'] = $this->params['sort'] ?? 0;       //排序
        $params['sort'] = (int) $params['sort'];
        $params['created_at'] = $this->params['created_at'] ?? 0;   //创建时间
        $params['created_at'] = (int) $params['created_at'];
        $params['suggest'] = $this->params['suggest'] ?? '';        //建议
        $params['sales_num'] = $this->params['sales_num'] ?? 0;     //销售数
        $params['sales_num'] = (int) $params['sales_num'];
        $params['score_num'] = $this->params['score_num'] ?? 0;     //评论数
        $params['score_num'] = (int) $params['score_num'];


        if (!$params['type']) {
            throw new UnprocessableEntityHttpException(850015);
        }
        if (!$params['id']) {
            throw new UnprocessableEntityHttpException(850016);
        }
        if (!$params['name']) {
            throw new UnprocessableEntityHttpException(850017);
        }
        if (!$params['intro']) {
            throw new UnprocessableEntityHttpException(850019);
        }
        if (!$params['lon']) {
            throw new UnprocessableEntityHttpException(850022);
        }
        if (!$params['lat']) {
            throw new UnprocessableEntityHttpException(850023);
        }
        if (!$params['created_at']) {
            throw new UnprocessableEntityHttpException(850025);
        }
        if (!$params['img']) {
            throw new UnprocessableEntityHttpException(850028);
        }


        $params['id'] = $params['type'].'-'.$params['id'];

//        $params = array(
//            'id' => '1',
//            'type' => 1,
//            'name' => '杭州',
//            'address' => '浙江杭州西湖56号',
//            'img' => '123.jpg',
//            'phone' => '0571889988',
//            'price' => 109.55,
//            'intro' => '简称“杭”，浙江省省会、副省级市，位于中国东南沿海、浙江省北部、钱塘江下游、京杭大运河南端，是浙江省的政治、经济、文化、教育、交通和金融中心，长江三角洲城市群中心城市之一、长三角宁杭生态经济带节点城市、中国重要的电子',
//            'score' => 4.5,
//            'evaluation' => 8933,
//            'lon' => 120.143051,
//            'lat' => 30.246092,
//            'geohash' => 'asjdfoiajeoifjaiowef',
//            'open_time' => '早8:00-晚18:00',
//            'sort' => 1,
//            'created_at' => time(),
//            'suggest' => '1-2天',
//            'sales_num' => 18,
//            'score_num' => 8933,
//        );

        $res = $this->XSService::update($params);
        return ['msg'=>'操作成功'];

    }

    //添加文档到索引
    public function addIndex()
    {
        $params['type'] = $this->params['type'] ?? 0;   //类型 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $params['type'] = (int) $params['type'];
        $params['id'] = $this->params['id'] ?? 0;       //主键
        $params['id'] = (int) $params['id'];
        $params['name'] = $this->params['name'] ?? '';  //名称
        $params['address'] = $this->params['address'] ?? ''; //地址
        $params['img'] = $this->params['img'] ?? '';        //图片
        $params['phone'] = $this->params['phone'] ?? '';    //电话
        $params['price'] = $this->params['price'] ?? 0.00;  //价格
        $params['price'] = (float) $params['price'];
        $params['intro'] = $this->params['intro'] ?? '';    //简介
        $params['score'] = $this->params['score'] ?? 0.00;  //评分
        $params['score'] = (float) $params['score'];
        $params['evaluation'] = $this->params['evaluation'] ?? 0; //评价
        $params['evaluation'] = (int) $params['evaluation'];
        $params['lon'] = $this->params['lon'] ?? 0.00;      //经度
        $params['lon'] = (float) $params['lon'];
        $params['lat'] = $this->params['lat'] ?? 0.00;      //纬度
        $params['lat'] = (float) $params['lat'];
        $params['geohash'] = $this->params['geohash'] ?? '';        //经纬度换算的字符串
        $params['open_time'] = $this->params['open_time'] ?? '';    //开放时间
        $params['sort'] = $this->params['sort'] ?? 0;       //排序
        $params['sort'] = (int) $params['sort'];
        $params['created_at'] = $this->params['created_at'] ?? 0;   //创建时间
        $params['created_at'] = (int) $params['created_at'];
        $params['suggest'] = $this->params['suggest'] ?? '';        //建议
        $params['sales_num'] = $this->params['sales_num'] ?? 0;     //销售数
        $params['sales_num'] = (int) $params['sales_num'];
        $params['score_num'] = $this->params['score_num'] ?? 0;     //评论数
        $params['score_num'] = (int) $params['score_num'];


        if (!$params['type']) {
            throw new UnprocessableEntityHttpException(850015);
        }
        if (!$params['id']) {
            throw new UnprocessableEntityHttpException(850016);
        }
        if (!$params['name']) {
            throw new UnprocessableEntityHttpException(850017);
        }
        if (!$params['intro']) {
            throw new UnprocessableEntityHttpException(850019);
        }
        if (!$params['lon']) {
            throw new UnprocessableEntityHttpException(850022);
        }
        if (!$params['lat']) {
            throw new UnprocessableEntityHttpException(850023);
        }
        if (!$params['created_at']) {
            throw new UnprocessableEntityHttpException(850025);
        }
        if (!$params['img']) {
            throw new UnprocessableEntityHttpException(850028);
        }


        $params['id'] = $params['type'].'-'.$params['id'];

//        $params = array(
//            'id' => '1',
//            'type' => 1,
//            'name' => '杭州',
//            'address' => '浙江杭州西湖56号',
//            'img' => '123.jpg',
//            'phone' => '0571889988',
//            'price' => 109.55,
//            'intro' => '简称“杭”，浙江省省会、副省级市，位于中国东南沿海、浙江省北部、钱塘江下游、京杭大运河南端，是浙江省的政治、经济、文化、教育、交通和金融中心，长江三角洲城市群中心城市之一、长三角宁杭生态经济带节点城市、中国重要的电子',
//            'score' => 4.5,
//            'evaluation' => 8933,
//            'lon' => 120.143051,
//            'lat' => 30.246092,
//            'geohash' => 'asjdfoiajeoifjaiowef',
//            'open_time' => '早8:00-晚18:00',
//            'sort' => 1,
//            'created_at' => time(),
//            'suggest' => '1-2天',
//            'sales_num' => 18,
//            'score_num' => 8933,
//        );

        $res = $this->XSService::add($params);
        return ['msg'=>'操作成功'];

    }


    //删除文档
    public function delIndex()
    {
        $arr = [Cid::CID_TYPE_A,Cid::CID_TYPE_B,Cid::CID_TYPE_C,Cid::CID_TYPE_D,Cid::CID_TYPE_E,Cid::CID_TYPE_F];
        $type = $this->params['type'] ?? 0; //类型 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $type = (int)$type;
        $id = $this->params['id'] ?? 0; //id
        $id = (int)$id;
        if (!in_array($type, $arr)) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$id) {
            throw new UnprocessableEntityHttpException(850005);
        }

        $res = $this->XSService::del($type.'-'.$id);

        return ['msg'=>'操作成功'];
    }

    //搜索
    public function search()
    {

        $key = $this->params['key'] ?? '';
        if (!$key) {
            throw new UnprocessableEntityHttpException(850029);
        }

        $res = $this->XSService::search($key);

        return $res;

    }

    //搜索建议
    public function suggest()
    {
        $key = $this->params['key'] ?? '';
        if (!$key) {
            throw new UnprocessableEntityHttpException(850029);
        }
        $res = $this->XSService::suggest($key);
        return $res;

    }


}
