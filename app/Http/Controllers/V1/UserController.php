<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\UserService;
use App\Services\SmsService;
use App\Services\YanzhenService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class UserController
 * @package App\Http\Controllers\V1
 */
class UserController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $params;
    protected $yanzhenService;
    protected $smsService;
    protected $userService;

    public function __construct(
        UserService $userService,
        TokenService $tokenService,
        Request $request,
        YanzhenService $yanzhenService,
        SmsService $smsService
    )
    {

        parent::__construct();
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->yanzhenService = $yanzhenService;
        $this->smsService = $smsService;

        //检测用户是否登录
        if(!$this->userId){
            throw new UnprocessableEntityHttpException(850000);
        }

        //接受到的参数
        $this->params = $this->request->all();

    }

    //获取用户信息
    public function userInfo()
    {
        $userInfo = $this->userService->getUserInfo($this->userId);
        return response_success($userInfo);
    }

    //修改经纬度
    public function editLbs()
    {
        $this->params['user_lon'] = $this->params['user_lon'] ?? ''; //经度
        $this->params['user_lat'] = $this->params['user_lat'] ?? ''; //纬度


        if (!$this->params['user_lon']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_lat']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {

            //登录注册
            $userId = $this->userService->editLbs($this->userId,$this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('修改经纬度异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '修改成功']);
    }

    //编辑用户信息
    public function editUserInfo()
    {
        $this->params['user_nickname'] = $this->params['user_nickname'] ?? ''; //昵称
        $this->params['user_sex'] = $this->params['user_sex'] ?? ''; //性别
        $this->params['user_sex'] = (int)$this->params['user_sex']; //openid
        $this->params['user_avatar'] = $this->params['user_avatar'] ?? ''; //头像
        $this->params['user_mobile'] = $this->params['user_mobile'] ?? ''; //手机


        if (!$this->params['user_nickname']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_avatar']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_mobile']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        //验证手机号
        if(!$this->yanzhenService::isMobile($this->params['user_mobile'])){
            throw new UnprocessableEntityHttpException(850009);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {

            $userId = $this->userService->editUserInfo($this->userId,$this->params);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('编辑用户信息异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '修改成功']);
    }

    public function logout()
    {
        $bearerToken = $this->request->server->getHeaders()['AUTHORIZATION'] ?? '';
        $claims = $this->tokenService->getJwtClaims($bearerToken);
        $this->tokenService->revokeToken($claims['jti']);

        return response_success(['msg' => '退出成功']);
    }


}
