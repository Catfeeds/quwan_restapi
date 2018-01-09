<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Cid;
use App\Models\CidMap;
use App\Models\Attractions;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Hall;
use App\Models\Fav;
use App\Models\Holiday;
use App\Models\Hotel;
use App\Models\Img;
use App\Models\Route;
use App\Models\RouteDayJoin;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class XSService
{

    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;

    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $hall;
    protected $fav;
    protected $hotel;
    protected $holiday;
    protected $user;

    public function __construct(
        User $user,
        Holiday $holiday,
        Hotel $hotel,
        Fav $fav,
        Hall $hall,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {

        $this->user = $user;
        $this->holiday = $holiday;
        $this->hotel = $hotel;
        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    //清空索引
    public static function clean()
    {

        try {
            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

            //初始化索引
            $index = $xs->index;

            // 执行清空操作
            $tag = $index->clean();


            //刷新索引缓存
            $index->flushIndex();
            sleep(2);

            //刷新搜索日志
            $index->flushLogging();
            sleep(2);
            return response_success(['msg' => $tag]);

        } catch (\XSException $e) {
            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
        }
    }

    //job更新文档
    public static function jobEditIndex($params)
    {
        Log::error('队列进来',$params);
        //查找内容 1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $addData = self::getInfo($params['type'], $params['id']);
        $res = self::update($addData);

        return $res;
    }

    //删除文档
    public static function del($params)
    {

        Log::info('索引删除开始=======================');
        Log::info('参数',$params);
        try {
            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

            //初始化索引
            $index = $xs->index;



            //删除
            $tag = $index->del($params);

            //刷新索引缓存
            $index->flushIndex();
            sleep(2);

            //刷新搜索日志
            $index->flushLogging();
            sleep(2);


            return response_success(['msg' => $tag]);

        } catch (\XSException $e) {
            Log::info('索引删除异常',['msg' => $e->getTraceAsString()]);
            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
        }

        Log::info('索引删除结束=======================');
    }

    //添加文档
    public static function add($params)
    {

//'id' => '主键id',
//'type' => '类型',
//'name' => '名称',
//'address' => '地址',
//'img' => '图片',
//'phone' => '电话',
//'price' => '价格',
//'intro' => '介绍',
//'score' => '评分数',
//'evaluation' => '评价数',
//'lon' => '经度',
//'lat' => '纬度',
//'geohash' => '通过经纬度换算得到的字符串索引',
//'open_time' => '开放时间',
//'sort' => '排序(从小到大)',
//'created_at' => '创建时间',
//'suggest' => '建议',
//'sales_num' => '销售数(目的地详情页需要用)',
//'score_num' => '景点评论数',


//        //导入数据索引
//        php ./Indexer.php --rebuild --source=mysql://vpgame:vpgame_hangzhouweipei2015@192.168.1.8/vpgame --sql="SELECT * FROM article" --project=article4
//
//        //强制停止重建
//        php ./Indexer.php --stop-rebuild article4
//
//        //查看索引状态
//        php ./Indexer.php --info -p  article4
//
//        //强制刷新 demo 项目的搜索日志
//        php ./Indexer.php --flush-log --project article4
//
//        //清空 demo 项目的索引数据
//        php ./Indexer.php --clean article4


        // if (!$params['id'] || !$params['author'] || !$params['title'] || !$params['content'] || !$params['post_time']) {
//            throw new UnprocessableEntityHttpException(850005);
        // }

        try {
            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

            //初始化索引
            $index = $xs->index;

            // 创建文档对象
            $doc = new \XSDocument();

            //获取分类id

            $doc->setFields($params);

            //添加到索引
            $tag = $index->add($doc);

            //刷新索引缓存
            $index->flushIndex();
            sleep(2);

            //刷新搜索日志
            $index->flushLogging();
            sleep(2);
            return response_success(['msg' => $tag]);

        } catch (\XSException $e) {

            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
//            echo $e;               // 直接输出异常描述
//            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
//            {
//                echo "\n" . $e->getTraceAsString() . "\n";
//            }
        }
    }


    //修改文档
    public static function update($params)
    {

//'id' => '主键id',
//'type' => '类型',
//'name' => '名称',
//'address' => '地址',
//'img' => '图片',
//'phone' => '电话',
//'price' => '价格',
//'intro' => '介绍',
//'score' => '评分数',
//'evaluation' => '评价数',
//'lon' => '经度',
//'lat' => '纬度',
//'geohash' => '通过经纬度换算得到的字符串索引',
//'open_time' => '开放时间',
//'sort' => '排序(从小到大)',
//'created_at' => '创建时间',
//'suggest' => '建议',
//'sales_num' => '销售数(目的地详情页需要用)',
//'score_num' => '景点评论数',


//        //导入数据索引
//        php ./Indexer.php --rebuild --source=mysql://vpgame:vpgame_hangzhouweipei2015@192.168.1.8/vpgame --sql="SELECT * FROM article" --project=article4
//
//        //强制停止重建
//        php ./Indexer.php --stop-rebuild article4
//
//        //查看索引状态
//        php ./Indexer.php --info -p  article4
//
//        //强制刷新 demo 项目的搜索日志
//        php ./Indexer.php --flush-log --project article4
//
//        //清空 demo 项目的索引数据
//        php ./Indexer.php --clean article4


        // if (!$params['id'] || !$params['author'] || !$params['title'] || !$params['content'] || !$params['post_time']) {
//            throw new UnprocessableEntityHttpException(850005);
        // }
        Log::info('索引更新开始=======================');
        Log::info('参数',$params);
        try {
            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

            //初始化索引
            $index = $xs->index;

            // 创建文档对象
            $doc = new \XSDocument();



            $doc->setFields($params);

            //添加到索引
            $tag = $index->update($doc);

            //刷新索引缓存
            $index->flushIndex();
            sleep(2);

            //刷新搜索日志
            $index->flushLogging();
            sleep(2);


            return response_success(['msg' => $tag]);

        } catch (\XSException $e) {
            Log::info('索引更新异常',['msg' => $e->getTraceAsString()]);

            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
//            echo $e;               // 直接输出异常描述
//            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
//            {
//                echo "\n" . $e->getTraceAsString() . "\n";
//            }
        }
        Log::info('索引更新结束=======================');
    }

    //搜索
    public static function search($params)
    {
        try {
            $search_begin = microtime(true); //开始执行搜索时间

            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

            $key = $params['key'];
            if (substr_count($key, ' ')) {
                $logKey = $key;
                $key = str_replace(' ', 'AND', $key);
                //var_dump('-------连词-------', $key);
            } else {
                //分词 setIgnore过滤标点 setMulti分词长短 getResult获取分词结果
                $tokenizer = new \XSTokenizerScws();
                $key = $tokenizer->setIgnore(true)->setMulti(5)->getResult($key);
                $key = array_pluck($key, 'word');
                $logKey = implode(' ', $key);
                $key = implode('OR', $key);
                //var_dump('-------分词-------', $key);
            }
            $key = '('.$key.')';

            $search = $xs->search;

            $search->setFuzzy(true); //开启模糊搜索

            //$search->setQuery('type:"2" AND '.$key);

            //$search->setScwsMulti(8);//搜索语句的分词等级[与setFuzzy使用相互排斥]

            //排序 表示先以 chrono 正序、再以 pid 逆序(pid 是字符串并不是数值所以 12 会排在 3 之后)
            //$sorts = array('chrono' => true, 'pid' => false);
            //$search->setMultiSort($sorts);



            //$words = $search->getHotQuery(50, 'total'); //热门词
            //var_dump('-------热门词-------', $words);

            //$words = $search->getRelatedQuery($key, 10);//相关搜索
            //var_dump('-------相关搜索-------', $words);

            //$docs = $search->getExpandedQuery($key); //搜索建议
            //var_dump('--------搜索建议------', $docs);


            //$docs = $search->terms($key); //高亮搜索词
            //var_dump('--------高亮搜索词------', $docs);

            //$search->addWeight('title', $this->params['key']); //增加关键字权重

            //搜索分类id
            if($params['cid']){
                //获取所有关联分类的景点id
                $attractionsIds = CidMap::getJoinIds($params['cid'], CidMap::CID_MAP_TYPE_1);
                if(true === empty($attractionsIds)){
                    return ['_count'=>0,'data'=>[]];
                }

                $tmp = ' AND (';
                foreach ($attractionsIds as $keyA => $valueA) {
                    $tmp .= 'id:'.CidMap::CID_MAP_TYPE_1.'-'.$valueA.' ';
                }
                $tmp .= ')';
                $key .= $tmp;
            }

            //[0全部,1景点,2目的地，3路线,4节日，5酒店,6餐厅]
            if($params['filter']){
                $key .= ' AND (type:'.$params['filter'].')';
            }



            $count = $search->count($key);
            //var_dump('-------搜索匹配总数-------', $count);

            //距离优先 在保证基本相关的情况下，距离越近的越靠前
            if($params['sortby'] === 'distance'){
                //经纬度排序 lon 代表经度、lat 代表纬度 必须将经度定义在前纬度在后
                $geo = array('lon' => $params['lon'], 'lat' => $params['lat']);
                $search->setGeodistSort($geo);
            }

            //评分优先 在保证基本相关的情况下，评分越高的越靠前
            if($params['sortby'] === 'score'){
                $sorts = array('score' => false);
                // 设置搜索排序
                $search->setMultiSort($sorts);
            }

            //分页
            $limit = $params['limit'] ?? 12; //每页显示数
            $offset = $params['offset'] ?? 1; //页码
            $offset = ($offset - 1) * $limit;
            $search->setLimit($limit, $offset);

            //$search->addWeight('type', '2');

            $docs = $search->search($key); //执行搜索
            $log = $search->getQuery($key); //搜索语句
            //var_dump('-------sql-------', $log,$key);

            $search_cost = microtime(true) - $search_begin; //执行结束时间
            //var_dump('-------执行时间-------', $search_cost);

            $arr = [];
            if (false === empty($docs)) {
                foreach ($docs as $key => $value) {
                    $valArr = $value->getFieldsArray();
                    $valArr['id'] = substr($valArr['id'], strrpos( $valArr['id'] ,'-')+1);

                    //@todo 注意图片处理
                    //$valArr['img'] = substr($valArr['id'], strrpos( $valArr['id'] ,'-')+1);
                    //$arr[] = $valArr;

                    if(false === empty($params['lon'])){
                        $distance = round($xs::geoDistance($params['lon'], $params['lat'], $valArr['lon'], $valArr['lat']));
                    }
                    $arr[] = [
                        'id' => $valArr['id'] ?? 0,
                        'type' => $valArr['type'] ?? 0,
                        'cid' => isset($valArr['cid']) ? json_decode($valArr['cid'],true) : [],
                        'name' => $valArr['name'] ?? '',
                        //'address' => $valArr['address'] ?? '',
                        'img' => $valArr['img'] ?? '',
                        //'phone' => $valArr['phone'] ?? '',
                        'price' => $valArr['price'] ?? 0,
                        'intro' => $valArr['intro'] ?? '',
                        'score' => $valArr['score'] ?? 0,
                        'evaluation' => $valArr['evaluation'] ?? 0,
                        'lon' => $valArr['lon'] ?? '',
                        'lat' => $valArr['lat'] ?? '',
                        'distance' => $distance ?? '',
                        //'geohash' => $valArr['geohash'] ?? '',
                        //'open_time' => $valArr['open_time'] ?? '',
                        //'sort' => $valArr['sort'] ?? 0,
                        //'created_at' => $valArr['created_at'] ?? 0,
                        //'suggest' => $valArr['suggest'] ?? '',
                        //'sales_num' => $valArr['sales_num'] ?? 0,
                        //'score_num' => $valArr['score_num'] ?? 0,
                        //迅搜
                        //docid() 取得搜索结果文档的 docid 值 (实际数据库内的 id，一般用不到)
                        //rank() 取得搜索结果文档的序号值 (第X条结果)
                        //percent() 取得搜索结果文档的匹配百分比 (结果匹配度, 1~100)
                        //weight() 取得搜索结果文档的权重值 (浮点数)
                        //ccount() 取得搜索结果折叠的数量 (按字段折叠搜索时才有效)
                        'xs_docid' => $valArr['docid'] ?? 0,
                        'xs_rank' => $valArr['rank'] ?? 0,
                        'xs_ccount' => $valArr['ccount'] ?? 0,
                        'xs_percent' => $valArr['percent'] ?? 0,
                        'xs_weight' => $valArr['weight'] ?? 0,
                        'xs_charset' => $valArr['charset'] ?? 0,
                    ];
                }
            }
            //var_dump('-------结果-------', $arr);

            //添加搜索记录到缓存去
            $search->addSearchLog($logKey);
            //刷新搜索日志
            $xs->index->flushLogging();

            return ['_count'=>$count,'data'=>$arr];

        } catch (\XSException $e) {
            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
        }

    }


    //搜索建议
    public static function suggest($key)
    {

        try {


            $search_begin = microtime(true); //开始执行搜索时间

            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);


            if (substr_count($key, ' ')) {
                $logKey = $key;
                $key = str_replace(' ', 'AND', $key);
            } else {
                //分词 setIgnore过滤标点 setMulti分词长短 getResult获取分词结果
                $tokenizer = new \XSTokenizerScws();
                $key = $tokenizer->setIgnore(true)->setMulti(5)->getResult($key);
                $key = array_pluck($key, 'word');
                $logKey = implode(' ', $key);
                $key = implode('OR', $key);
            }

            $search = $xs->search;

            $search->setFuzzy(true); //开启模糊搜索


            $docs = $search->getExpandedQuery($key); //搜索建议

            return $docs;

        } catch (\XSException $e) {
            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
        }

    }

    //获取数据
    public static function getInfo($type, $joinId){
        switch ($type) {
            case 1: //景点
                $info = self::getAttractionData($joinId);
                break;
            case 2: //目的地
                $info = [];
                break;
            case 3: //线路
                $info = self::getRouteData($joinId);
                break;
            case 4: //节日
                $info = self::getHolidayData($joinId);
                break;
            case 5: //酒店
                $info = self::getHotelData($joinId);
                break;
            case 6: //餐厅
                $info = self::getHallData($joinId);
                break;
            default: $info = []; break;
        }

        if(true === empty($info)){
            throw new UnprocessableEntityHttpException(850004);
        }

        $info['id'] = $info['type'].'-'.$info['id'];

        echo($info['intro']);
        $info['intro'] = strip_tags($info['intro']);
        echo($info['intro']);die;

        //分类处理
        $info['cid'] = '';
        $cid = CidMap::getCidsInfo($info['id'], $info['type']);
        if (false === empty($cid)) {
            $info['cid'] = json_encode($cid);
        }

        $info['img'] = $info['img'][0] ?? '';

        return $info;
    }

    /**
     * 线路数据
     * @param $value
     * @return array
     */
    public static function getRouteData($routeId)
    {
        $info = Route::getInfo($routeId);
        $res = [];
        if (false === empty($info)) {
            //图片
            $img = RouteDayJoin::getOneJoinImg($routeId);

            $res = [
                'id' => $info['route_id'],
                'type' => 3,
                'name' => $info['route_name'],
                'img' => $img,
                'price' => '',
                'intro' => $info['route_intro'],
                'score' => $info['route_day_num'],
                'evaluation' => '',
                'lon' => '',
                'lat' => '',
            ];
        }
        return $res;
    }
    
    /**
     * 景点数据
     * @param $value
     * @return array
     */
    public static function getAttractionData($attractionsId)
    {
        $info = Attractions::getInfo($attractionsId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'id' => $info['attractions_id'],
                'type' => Fav::FAV_TYPE_A,
                'name' => $info['attractions_name'],
                'img' => $info['img'],
                'price' => $info['attractions_price'],
                'intro' => $info['attractions_intro'],
                'score' => $info['attractions_score'],
                'evaluation' => $info['attractions_evaluation'],
                'lon' => $info['attractions_lon'],
                'lat' => $info['attractions_lat'],
            ];
        }
        return $res;
    }

    /**
     * 节日数据
     * @param $value
     * @return array
     */
    public static function getHolidayData($holidayId)
    {
        $info = Holiday::getInfoData($holidayId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'id' => $info['holiday_id'],
                'type' => Fav::FAV_TYPE_B,
                'name' => $info['holiday_name'],
                'img' => $info['img'],
                'price' => $info['holiday_price'],
                'intro' => $info['holiday_intro'],
                'score' => $info['holiday_score'],
                'evaluation' => $info['holiday_evaluation'],
                'lon' => $info['holiday_lon'],
                'lat' => $info['holiday_lat'],
            ];
        }
        return $res;
    }

    /**
     * 酒店数据
     * @param $value
     * @return array
     */
    public static function getHotelData($hotelId)
    {
        $info = Hotel::getInfo($hotelId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'id' => $info['hotel_id'],
                'type' => Fav::FAV_TYPE_C,
                'name' => $info['hotel_name'],
                'img' => $info['img'],
                'price' => $info['hotel_price'],
                'intro' => $info['hotel_intro'],
                'score' => $info['hotel_score'],
                'evaluation' => $info['hotel_evaluation'],
                'lon' => $info['hotel_lon'],
                'lat' => $info['hotel_lat'],
            ];
        }
        return $res;
    }

    /**
     * 餐厅数据
     * @param $value
     * @return array
     */
    public static function getHallData($hallId)
    {
        $info = Hall::getInfo($hallId);
        $res = [];
        if (false === empty($info)) {
            $res = [
                'id' => $info['hall_id'],
                'type' => Fav::FAV_TYPE_D,
                'name' => $info['hall_name'],
                'img' => $info['img'],
                'price' => $info['hall_price'],
                'intro' => $info['hall_intro'],
                'score' => $info['hall_score'],
                'evaluation' => $info['hall_evaluation'],
                'lon' => $info['hall_lon'],
                'lat' => $info['hall_lat'],
            ];
        }
        return $res;
    }
}