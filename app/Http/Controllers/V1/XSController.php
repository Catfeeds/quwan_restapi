<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\Cid;
use App\Models\CidMap;
use App\Models\User;
use App\Services\FavService;
use App\Services\SmsService;
use App\Services\XSService;
use App\Services\YanzhenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;

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


    //参看索引的文档总量
    public function getDbTotal()
    {

        $res = $this->XSService::getDbTotal();
        return $res;

    }


    //清空索引
    public function cleanIndex()
    {

        //清空索引
        $res = $this->XSService::clean();
        return $res;

    }

    //修改文档
    public function editIndex()
    {
        Log::info('索引更新开始=======================');
        $params['type'] = $this->params['type'] ?? 0;   //类型 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $params['type'] = (int) $params['type'];
        $params['id'] = $this->params['id'] ?? 0;       //主键
        $params['id'] = (int) $params['id'];

        Log::info('参数',$params);
        if (!$params['type']) {
            throw new UnprocessableEntityHttpException(850015);
        }
        if (!$params['id']) {
            throw new UnprocessableEntityHttpException(850016);
        }

        //查找内容 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $addData = $this->XSService::getInfo($params['type'], $params['id']);

        $res = $this->XSService::update($addData);
        return ['msg'=>'操作成功'];

    }

    //添加文档到索引
    public function addIndex()
    {

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
        Log::info('索引更新开始=======================');



        $params['type'] = $this->params['type'] ?? 0;   //类型 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $params['type'] = (int) $params['type'];
        $params['id'] = $this->params['id'] ?? 0;       //主键
        $params['id'] = (int) $params['id'];
        Log::info('参数',$params);
        if (!$params['type']) {
            throw new UnprocessableEntityHttpException(850015);
        }
        if (!$params['id']) {
            throw new UnprocessableEntityHttpException(850016);
        }

        //查找内容 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $addData = $this->XSService::getInfo($params['type'], $params['id']);


        $res = $this->XSService::update($addData);
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

        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];



        $this->params['key'] = $this->params['key'] ?? '';

        $this->params['filter'] = $this->params['filter'] ?? 0; //搜索对象 [0全部,1景点,2目的地，3路线,4节日，5酒店,6餐厅]
        $this->params['filter'] = (int)$this->params['filter'];

        $this->params['sortby'] = $this->params['sortby'] ?? 'key'; //搜索排序 [key相关优先, distance距离优先, score评分优先]


        $this->params['lon'] = $this->params['lon'] ?? ''; //用户经度
        $this->params['lat'] = $this->params['lat'] ?? ''; //用户纬度

        //获取用户经纬度
        if ($this->userId)
        {
            $userLon = User::getUserLon($this->userId);
            Log::Info('用户:',$userLon);
            if (false === empty($userLon))
            {
                $this->params['lon'] = $userLon['user_lon']; //用户经度
                $this->params['lat'] = $userLon['user_lat']; //用户纬度
            }
        }



        $this->params['cid'] = $this->params['cid'] ?? 0; //搜索分类id
        $this->params['cid'] = (int)$this->params['cid'];

        if (!$this->params['key']) {
            throw new UnprocessableEntityHttpException(850029);
        }

        $filterArr = [0,1,2,3,4,5,6];
        if (!in_array($this->params['filter'], $filterArr)) {
            throw new UnprocessableEntityHttpException(850045);
        }

        $sortbyArr = ['key', 'distance', 'score'];
        if (!in_array($this->params['sortby'], $sortbyArr)) {
            throw new UnprocessableEntityHttpException(850046);
        }


        $res = $this->XSService::search($this->params);

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
