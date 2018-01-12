<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Jobs\XssJob;
use App\Models\Score;
use App\Services\ScoreService;
use App\Services\XSService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ScoreController
 * @package App\Http\Controllers\V1
 */
class ScoreController extends Controller
{
    protected $tokenService;
    protected $request;
    protected $params;
    protected $scoreService;

    public function __construct(TokenService $tokenService, Request $request, ScoreService $scoreService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->scoreService = $scoreService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index()
    {
        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];

        $this->params['score_type'] = $this->params['score_type'] ?? 0; //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $this->params['score_type'] = (int)$this->params['score_type'];

        $this->params['join_id'] = $this->params['join_id'] ?? 0; //关联id
        $this->params['join_id'] = (int)$this->params['join_id'];


        $arr = [Score::SCORE_TYPE_A, Score::SCORE_TYPE_B, Score::SCORE_TYPE_C, Score::SCORE_TYPE_D,Score::SCORE_TYPE_E];
        if (!in_array($this->params['score_type'], $arr)) {
            throw new UnprocessableEntityHttpException(850005);
        }

        $data = $this->scoreService->getList($this->params);

        if (!$data) {
            $data = [
                'paging' => [
                    'limit' => 10,
                    'offset' => 0,
                    'total' => 0,
                ],
                'data' => []
            ];
        }
        return response_success($data);
    }


    public function add()
    {

        $this->params['score_type'] = $this->params['score_type'] ?? 0; //1景点,2目的地，3路线,4节日，5酒店,6餐厅
        $this->params['score_type'] = (int)$this->params['score_type'];

        $this->params['join_id'] = $this->params['join_id'] ?? 0; //评价对象id
        $this->params['join_id'] = (int)$this->params['join_id'];

        $this->params['order_id'] = $this->params['order_id'] ?? 0; //评价关联订单id
        $this->params['order_id'] = (int)$this->params['order_id'];

        $this->params['score'] = $this->params['score'] ?? 0; //评分
        $this->params['score'] = (int)$this->params['score'];

        $this->params['score_comment'] = $this->params['score_comment'] ?? ''; //评价内容
        $this->params['img'] = $this->params['img'] ?? ''; //评价图片,多个用英文,号连接
        $this->params['img'] = explode(',', $this->params['img']);

        $this->params['user_id'] = $this->params['user_id'] ?? 0; //用户id
        $this->params['user_id'] = (int)$this->params['user_id'];

        $this->params['score_from_id'] = $this->params['score_from_id'] ?? ''; //模板id(后台回复评论消息用)

        Log::error('添加评价参数: ', $this->params);

        $arr = [Score::SCORE_TYPE_A, Score::SCORE_TYPE_B, Score::SCORE_TYPE_C, Score::SCORE_TYPE_D,Score::SCORE_TYPE_E];
        if (!in_array($this->params['score_type'], $arr)) {
            throw new UnprocessableEntityHttpException(850005);
        }

        if (!$this->params['score_from_id']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        if (!$this->params['join_id']) {
            throw new UnprocessableEntityHttpException(850005);
        }
//        if (!$this->params['order_id']) {
//            throw new UnprocessableEntityHttpException(850005);
//        }
        if (!$this->params['score']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['score_comment']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        //少于10个字提示 请输入大于10个字的评价
        if (cn_strlen($this->params['score_comment']) < 10) {
            throw new UnprocessableEntityHttpException(850011);
        }

        //最多4张图片
        if (count($this->params['img']) > 4) {
            throw new UnprocessableEntityHttpException(850012);
        }


        DB::connection('db_quwan')->beginTransaction();
        try {
            $res = $this->scoreService->addScore($this->params);

            XSService::jobEditIndex(['type'=>$this->params['score_type'], 'id' => $this->params['join_id']]);

            DB::connection('db_quwan')->commit();

            //调用队列并执行
            //$job = new XssJob(['type'=>$this->params['score_type'], 'id' => $this->params['join_id']]);
            //$res = $this->dispatch($job);


            //var_dump($res);

        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('发布评价异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '感谢您的评价']);
    }
}
