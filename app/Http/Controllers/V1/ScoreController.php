<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Score;
use App\Services\ScoreService;
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

    public function __construct(TokenService $tokenService, Request $request,ScoreService $scoreService)
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

        $this->params['score_type'] = $this->params['score_type'] ?? 0; //1景点,2节日，3酒店,4餐厅
        $this->params['score_type'] = (int)$this->params['score_type'];


        $arr = [Score::SCORE_TYPE_1,Score::SCORE_TYPE_2,Score::SCORE_TYPE_3,Score::SCORE_TYPE_4];
        if (!in_array($this->params['score_type'], $arr)) {
         throw new UnprocessableEntityHttpException(850005);
        }

        $data = $this->scoreService->getList($this->params);

        if(!$data){
            $data = [
                'paging'=>[
                    'limit'=>10,
                    'offset'=>0,
                    'total'=>0,
                ],
                'data' => []
            ];
        }
        return response_success($data);
    }

}
