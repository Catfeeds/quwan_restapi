<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;

class XSService
{

    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;

    public function __construct()
    {



    }

    //删除文档
    public static function del($params)
    {

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
            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
        }
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

            throw new UnprocessableEntityHttpException(850014, [], '', ['msg' => $e->getTraceAsString()]);
//            echo $e;               // 直接输出异常描述
//            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
//            {
//                echo "\n" . $e->getTraceAsString() . "\n";
//            }
        }
    }

    //搜索
    public static function search($key)
    {
        try {
            $search_begin = microtime(true); //开始执行搜索时间

            $indexName = config('xs.xs_index');
            $xs = new \XS($indexName);

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

            $search = $xs->search;

            $search->setFuzzy(true); //开启模糊搜索
            //$search->setScwsMulti(8);//搜索语句的分词等级[与setFuzzy使用相互排斥]

            //排序 表示先以 chrono 正序、再以 pid 逆序(pid 是字符串并不是数值所以 12 会排在 3 之后)
            //$sorts = array('chrono' => true, 'pid' => false);
            //$search->setMultiSort($sorts);

            //经纬度排序 lon 代表经度、lat 代表纬度 必须将经度定义在前纬度在后
            //$geo = array('lon' => 116.45, 'lat' => '39.96');
            //$search->setGeodistSort($geo);

            //$words = $search->getHotQuery(50, 'total'); //热门词
            //var_dump('-------热门词-------', $words);

            //$words = $search->getRelatedQuery($key, 10);//相关搜索
            //var_dump('-------相关搜索-------', $words);

            //$docs = $search->getExpandedQuery($key); //搜索建议
            //var_dump('--------搜索建议------', $docs);


            //$docs = $search->terms($key); //高亮搜索词
            //var_dump('--------高亮搜索词------', $docs);

            //$search->addWeight('title', $this->params['key']); //增加关键字权重

            $count = $search->count($key);
            //var_dump('-------搜索匹配总数-------', $count);

            $docs = $search->search($key); //执行搜索
            //$log = $search->getQuery($key); //搜索语句
            //var_dump('-------sql-------', $log);

            $search_cost = microtime(true) - $search_begin; //执行结束时间
            //var_dump('-------执行时间-------', $search_cost);

            $arr = [];
            if (false === empty($docs)) {
                foreach ($docs as $key => $value) {
                    $valArr = $value->getFieldsArray();
                    $valArr['id'] = substr($valArr['id'], strrpos( $valArr['id'] ,'-')+1);
                    //@todo 注意图片处理
                    //$valArr['img'] = substr($valArr['id'], strrpos( $valArr['id'] ,'-')+1);
                    $arr[] = $valArr;
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


    //搜索
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
}