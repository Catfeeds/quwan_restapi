<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Services\OrderService;
use App\Services\QiNiuService;
use App\Services\UserService;
use App\Services\SmsService;
use App\Services\YanzhenService;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController
 * @package App\Http\Controllers\V1
 */
class UserController extends Controller
{

    const CACHE_TAG = 'QUWAN'; //缓存模块tag
    public $userYzmCacheKey = 'quwan:user:yzm:%s'; //验证码key

    protected $tokenService;
    protected $request;
    protected $params;
    protected $yanzhenService;
    protected $smsService;
    protected $userService;
    protected $orderService;

    public function __construct(
        OrderService $orderService,
        UserService $userService,
        TokenService $tokenService,
        Request $request,
        YanzhenService $yanzhenService,
        SmsService $smsService
    )
    {

        parent::__construct();
        $this->orderService = $orderService;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->request = $request;
        $this->yanzhenService = $yanzhenService;
        $this->smsService = $smsService;


        //接受到的参数
        $this->params = $this->request->all();

    }

    //分享成功增加统计
    public function shareOk()
    {

        $this->params['join_id'] = $this->params['join_id'] ?? 0;
        $this->params['join_id'] = (int)$this->params['join_id'];
        $this->params['log_join_type'] = $this->params['log_join_type'] ?? 0;
        $this->params['log_join_type'] = (int)$this->params['log_join_type']; //1景点,2目的地，3路线,4节日，5酒店,6餐厅，
        if (!$this->params['join_id']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        if ($this->params['log_join_type'] <= 0 || $this->params['log_join_type'] >= 7 ) {
            throw new UnprocessableEntityHttpException(850005);
        }

        DB::connection('db_quwan')->beginTransaction();
        try {

            //记录登录日志
            $logArr = [
                'log_type' => \App\Models\Log::LOG_TYPE_2,
                'log_time' => time(),
                'user_id' => $this->userId,
                'log_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'join_id' => $this->params['join_id'],
                'log_join_type' => $this->params['log_join_type'],
            ];
            \App\Models\Log::create($logArr);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('分享成功增加统计异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '操作成功']);

    }


    //绑定用户手机
    public function bindMobile()
    {

        $yzm = $this->params['yzm'] ?? '';

        //检测验证码
        $userYzmCacheKey = sprintf($this->userYzmCacheKey, $this->userId);
        $oldYzm = Cache::tags(self::CACHE_TAG)->get($userYzmCacheKey);
        if($yzm !== $oldYzm){
            Cache::tags(self::CACHE_TAG)->forget($userYzmCacheKey);
            throw new UnprocessableEntityHttpException(850013);
        }

        $phone = $this->params['user_mobile'] ?? '';
        if (!$this->yanzhenService::isMobile($phone)) {
            throw new UnprocessableEntityHttpException(850009);
        }

        //绑定用户手机与状态
        DB::connection('db_quwan')->beginTransaction();
        try {

            $tag = $this->userService->bindMobile($this->userId,$phone);

            DB::connection('db_quwan')->commit();
        } catch (Exception $e) {
            DB::connection('db_quwan')->rollBack();

            //记错误日志
            Log::error('绑定用户手机异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(850002);
        }

        return response_success(['msg' => '绑定成功']);

    }


    //发送短信
    public function sendSms()
    {
        $phone = $this->params['user_mobile'] ?? '';
        if (!$this->yanzhenService::isMobile($phone)) {
            throw new UnprocessableEntityHttpException(850009);
        }

        //验证码
        $yzm = random_int(100000,999999);

        //存储到缓存中 1分钟
        $userYzmCacheKey = sprintf($this->userYzmCacheKey, $this->userId);
        Cache::tags(self::CACHE_TAG)->put($userYzmCacheKey, $yzm, 1);

        //发送短信
        $templId = 58476; //模板id
        $params = [$yzm];
        $res = $this->smsService::send($templId, $phone, $params);

        return response_success(['msg'=>'发送成功']);

    }


    /**
     * 上传到7牛
     */
    public function qiniu()
    {
        $file = Input::file('file');
        if ($file === null) {
            throw new UnprocessableEntityHttpException(850005);
        }

        //检测是否上传成功
        if (!$file->isValid()) {
            throw new UnprocessableEntityHttpException(850006, [], '', ['msg' => $file->getError()]);
        }

        //大小限制1
        $uploadSize = config('qiniu.upload_size');
        if ($file->getClientSize() > $uploadSize) {
            throw new UnprocessableEntityHttpException(850007);
        }

        //类型限制
        $allowed_extensions = config('qiniu.extensions');
        var_dump($allowed_extensions);
        var_dump($file->getClientMimeType());
        die;
        if (!in_array($file->getClientMimeType(), $allowed_extensions)) {
            throw new UnprocessableEntityHttpException(850008);
        }

        $hz_name = substr($file->getClientOriginalName(), strrpos($file->getClientOriginalName(), ".") + 1);
        $destinationPath = 'uploads/imges/';
        $fileName = str_random(10) . '.' . $hz_name;

        //移动到指定文件夹
        $file->move($destinationPath, $fileName);

        list($qiniuUrl, $res) = QiNiuService::uploadQiniu($fileName, $destinationPath);

        return ['url' => $qiniuUrl, 'file_name' => $res[0]['key']];
    }


    //获取订单统计信息
    public function orderCount()
    {
        $userInfo = $this->orderService->getCountInfo($this->userId);
        return response_success($userInfo);
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
        $this->params['user_sex'] = $this->params['user_sex'] ?? 2; //性别
        $this->params['user_sex'] = (int)$this->params['user_sex']; //openid
        $this->params['user_avatar'] = $this->params['user_avatar'] ?? ''; //头像
        $this->params['user_mobile'] = $this->params['user_mobile'] ?? ''; //手机


        if (!$this->params['user_nickname']) {
            throw new UnprocessableEntityHttpException(850005);
        }
        if (!$this->params['user_avatar']) {
            throw new UnprocessableEntityHttpException(850005);
        }

        if ($this->params['user_mobile'] && !$this->yanzhenService::isMobile($this->params['user_mobile'])) {
            // throw new UnprocessableEntityHttpException(850005);

            //验证手机号
            // if(){
                throw new UnprocessableEntityHttpException(850009);
            // }
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
