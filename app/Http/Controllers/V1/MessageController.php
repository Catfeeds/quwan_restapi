<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class MessageController
 * @package App\Http\Controllers\V1
 */
class MessageController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $messageService;

    public function __construct(TokenService $tokenService, Request $request,MessageService $messageService)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->messageService = $messageService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    //所有消息已读
    public function messageRead()
    {
        DB::connection('db_quwan')->beginTransaction();
        try {
            $data = $this->messageService->setAllRead($this->userId);
            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('所有消息已读异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg'=>'操作成功']);
    }

    public function messageList()
    {

        $this->params['limit'] = $this->params['limit'] ?? 10;//每页显示数
        $this->params['limit'] = (int)$this->params['limit'];

        $this->params['offset'] = $this->params['offset'] ?? 1;//页码
        $this->params['offset'] = (int)$this->params['offset'];

        $this->params['user_id'] = (int)$this->userId;

        $data = $this->messageService->getListData($this->params);
        return response_success($data);
    }


    public function messageInfo($messageId)
    {

        $messageId = $messageId ?? 0;//每页显示数
        $messageId = (int)$messageId;

        $data = $this->messageService->getInfoData($messageId, $this->userId);
        return response_success($data);
    }

}
