<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\XS;
use App\Models\Img;
use App\Models\Route;

class XSService
{

    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;
    protected $params;
    protected $yanzhenService;

    public function __construct(
        XS $hall,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->hall = $hall;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    public function add($params)
    {

'id' => '主键id',
'type' => '类型',
'name' => '名称',
'address' => '地址',
'img' => '图片',
'phone' => '电话',
'price' => '价格',
'intro' => '介绍',
'score' => '评分数',
'evaluation' => '评价数',
'lon' => '经度',
'lat' => '纬度',
'geohash' => '通过经纬度换算得到的字符串索引',
'open_time' => '开放时间',
'sort' => '排序(从小到大)',
'created_at' => '0',
'suggest' => '建议',
'sales_num' => '销售数(目的地详情页需要用)',
'score_num' => '景点评论数',


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
            $xs = new \XS('article4');

            //初始化索引
            $index = $xs->index;

            // 创建文档对象
            $doc = new \XSDocument();

            $params['content'] = strip_tags($params['content']);
            $params['content'] = lose_space($params['content']);
            $data = array(
                'id' => $params['id'], // 此字段为主键，必须指定
                'author' => $params['author'], // 此字段为主键，必须指定
                'title' => $params['title'], // 此字段为主键，必须指定
                'content' => $params['content'], // 此字段为主键，必须指定
                'post_time' => $params['post_time'], // 此字段为主键，必须指定
                //'chrono' => time()
            );


            $doc->setFields($data);

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
            echo $e;               // 直接输出异常描述
            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
            {
                echo "\n" . $e->getTraceAsString() . "\n";
            }
        }
    }
}