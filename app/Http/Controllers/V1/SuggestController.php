<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\SuggestService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class SuggestController
 * @package App\Http\Controllers\V1
 */
class SuggestController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $suggestService;

    public function __construct(TokenService $tokenService, Request $request,SuggestService $suggestService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->suggestService = $suggestService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index()
    {
        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];

        $this->params['suggest_type'] = $this->params['suggest_type'] ?? 0; //1景点,2节日，3酒店,4餐厅
        $this->params['suggest_type'] = (int)$this->params['suggest_type'];


        $arr = [Suggest::SCORE_TYPE_A, Suggest::SCORE_TYPE_B, Suggest::SCORE_TYPE_C, Suggest::SCORE_TYPE_D];
        if (!in_array($this->params['suggest_type'], $arr)) {
            throw new UnprocessableEntityHttpException(850005);
        }

        $data = $this->suggestService->getList($this->params);

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

    //发布建议反馈
    public function addSuggest()
    {

        $this->params['suggest_comment'] = $this->params['suggest_comment'] ?? ''; //建议内容
        $this->params['suggest_phone'] = $this->params['suggest_phone'] ?? ''; //联系方式
        $this->params['img'] = $this->params['img'] ?? ''; //评价图片,多个用英文,号连接
        $this->params['img'] = explode(',', $this->params['img']);

        $this->params['user_id'] = $this->userId; //用户id


        Log::error('发布建议反馈参数: ', $this->params);

       
        if (!$this->params['suggest_comment']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        //少于10个字提示 请输入大于10个字的评价
        if (cn_strlen($this->params['suggest_comment']) < 10) {
            throw new UnprocessableEntityHttpException(850011);
        }

        //最多4张图片
        if (count($this->params['img']) > 4) {
            throw new UnprocessableEntityHttpException(850012);
        }


        DB::connection('db_quwan')->beginTransaction();
        try {
            $res = $this->suggestService->addSuggest($this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('发布建议反馈异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '感谢您的建议反馈']);
    }

}
