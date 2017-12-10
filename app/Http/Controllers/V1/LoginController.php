<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\LoginService;
use App\Services\SmsService;
use App\Services\YanzhenService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class LoginController
 * @package App\Http\Controllers\V1
 */
class LoginController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $yanzhenService;
    protected $smsService;
    protected $loginService;

    public function __construct(
        LoginService $loginService,
        TokenService $tokenService,
        Request $request,
        YanzhenService $yanzhenService,
        SmsService $smsService
    )
    {

        parent::__construct();
        $this->loginService = $loginService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->yanzhenService = $yanzhenService;
        $this->smsService = $smsService;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function login()
    {
        $this->params['openid'] = $this->params['openid'] ?? ''; //openid
        $this->params['user_nickname'] = $this->params['user_nickname'] ?? ''; //昵称
        $this->params['user_avatar'] = $this->params['user_avatar'] ?? ''; //头像
        $this->params['user_sex'] = $this->params['user_sex'] ?? 0; //性别
        $this->params['user_sex'] = (int)$this->params['user_sex']; //openid


        if (!$this->params['openid']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_nickname']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_avatar']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_sex']) {
            throw new UnprocessableEntityHttpException(850005);
        }


        DB::connection('db_quwan')->beginTransaction();
        try {

            //登录注册
            $userId = $this->loginService->login($this->params);

            //记录登录日志
            $logArr = [
                'log_type' => \App\Models\Log::LOG_TYPE_1,
                'log_time' => time(),
                'user_id' => $userId,
                'log_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ];
            \App\Models\Log::create($logArr);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('登录异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        //生成 token
        $token = $this->tokenService->createToken($userId, 'web');

        return response_success(['token' => $token]);
    }

    public function logout()
    {
        $bearerToken = $this->request->server->getHeaders()['AUTHORIZATION'] ?? '';
        $claims = $this->tokenService->getJwtClaims($bearerToken);
        $this->tokenService->revokeToken($claims['jti']);

        return response_success(['msg' => '退出成功']);
    }


}
