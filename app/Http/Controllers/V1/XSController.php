<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
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
        $this->yanzhenService = $XSService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //添加文档到索引
    public function addIndex()
    {
        $res = $this->XSService->add($this->params);

    }

    public function xs()
    {

        try {
            $key = $this->params['key'] ?? '';
            if (!$key) {
                throw new UnprocessableEntityHttpException(850005);
            }

            $search_begin = microtime(true); //开始执行搜索时间

            $xs = new \XS('article4');


            if (substr_count($key, ' ')) {
                $logKey = $key;
                $key = str_replace(' ', 'AND', $key);
                var_dump('-------连词-------', $key);
            } else {
                //分词 setIgnore过滤标点 setMulti分词长短 getResult获取分词结果
                $tokenizer = new \XSTokenizerScws();
                $key = $tokenizer->setIgnore(true)->setMulti(5)->getResult($key);
                $key = array_pluck($key, 'word');
                $logKey = implode(' ', $key);
                $key = implode('OR', $key);
                var_dump('-------分词-------', $key);
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

            $words = $search->getHotQuery(50, 'total'); //热门词
            var_dump('-------热门词-------', $words);

            $words = $search->getRelatedQuery($key, 10);//相关搜索
            var_dump('-------相关搜索-------', $words);

            $docs = $search->getExpandedQuery($key); //搜索建议
            var_dump('--------搜索建议------', $docs);


            $docs = $search->terms($key); //高亮搜索词
            var_dump('--------高亮搜索词------', $docs);

            //$search->addWeight('title', $this->params['key']); //增加关键字权重

            $count = $search->count($key);
            var_dump('-------搜索匹配总数-------', $count);

            $docs = $search->search($key); //执行搜索
            $log = $search->getQuery($key); //搜索语句
            var_dump('-------sql-------', $log);

            $search_cost = microtime(true) - $search_begin; //执行结束时间
            var_dump('-------执行时间-------', $search_cost);

            $arr = [];
            if (false === empty($docs)) {
                foreach ($docs as $key => $value) {
                    $arr[] = $value->getFieldsArray();
                }
            }
            var_dump('-------结果-------', $arr);

            //添加搜索记录到缓存去
            $search->addSearchLog($logKey);
            //刷新搜索日志
            $xs->index->flushLogging();

        } catch (\XSException $e) {
            echo $e;               // 直接输出异常描述
            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
            {
                echo "\n" . $e->getTraceAsString() . "\n";
            }
        }

    }


}
