<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\CustomException;
use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\EconomySteamItem;
use App\Models\Like;
use App\Models\LikeLog;
use App\Models\Roll;
use App\Models\RollConfig;
use App\Models\RollExtend;
use App\Models\RollExternalLink;
use App\Models\RollItem;
use App\Models\RollLog;
use App\Models\RollLogData;
use App\Models\RollPlayer;
use App\Models\RollPlayerItem;
use App\Models\RollTag;
use App\Models\User;
use App\Models\UserAttribute;
use App\Models\UserInventory;
use App\Models\UserInventoryHistory;
use App\Models\UserInventoryTransferHistory;
use App\Models\UserInventoryTransferHistoryMap;
use App\Models\UserProfile;
use App\Repository\ItemsRepository;
use App\Repository\RollRepository;
use App\Services\PlayersService;
use App\Services\RollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

/**
 * Class RollController
 * @package App\Http\Controllers\V1
 */
class RollController extends Controller
{
    /* 当前文件错误码 */
    const ERR = 101;
    const CACHE_TAG = 'ROLL'; //缓存模块tag
    const PWDERROR_NUM = 10; //交易每天密码输出次数
    const ROLL_PWDERROR_NUM = 6; //房间每天密码输出次数
    const ROLL_PAY_USERNUM = 5; //手动开奖限制每次最大输入中奖人数
    const ITEM_MAX_NUM = 100; //房间奖池数最大限制
    const PLAYERS_COUNT_MAX = 10; //当参与人大于等于多少人时候不能在删除饰品

    public $roll = null;
    public $request = null;
    public $rollRepository = null;
    public $rollPlayer = null;
    public $playersService = null;
    public $userModel = null;
    public $rollService = null;
    public $payCacheKey = 'roll:paypwd:error:%s'; //交易密码错误次数
    public $payLockCacheKey = 'roll:paypwd:error:locktime:%s'; //交易密码错误次数锁定时间范围
    public $rollCacheKey = 'roll:user:pwderror:%s'; //房间密码错误次数
    public $rollLockCacheKey = 'roll:user:pwderror:locktime:%s'; //房间密码错误次数锁定时间范围

    public $addItemLockCacheKey = 'roll:user:addtime:lock:%s'; //添加饰品并发控制
    public $delItemLockCacheKey = 'roll:user:deltiem:lock:%s'; //删除饰品并发控制
    public $rollPayLockCacheKey = 'roll:user:pay:lockt:%s'; //手动roll并发控制
    public $addPayLockCacheKey = 'roll:user:addpay:lockt:%s'; //参与报名并发控制
    public $addLikeLockCacheKey = 'roll:user:addLike:lockt:%s:%s'; //点赞并发控制
    public $delLikeLockCacheKey = 'roll:user:delLike:lockt:%s:%s'; //点踩并发控制

    public $rollListCacheKey = 'roll:list:data:%s'; //房间列表缓存

    public function __construct(
        Roll $roll,
        Request $request,
        RollRepository $rollRepository,
        RollPlayer $rollPlayer,
        PlayersService $playersService,
        User $userModel,
        RollService $rollService
    ) {

        parent::__construct();

        $this->roll = $roll;
        $this->request = $request;
        $this->rollRepository = $rollRepository;
        $this->rollPlayer = $rollPlayer;
        $this->playersService = $playersService;
        $this->userModel = $userModel;
        $this->rollService = $rollService;

        //检测是否开启ROLL功能
        $method = $this->request->method();
        $isOpen = RollConfig::getValue('control_roll');
        if ($isOpen !== 'yes' && $method !== 'GET') {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 3));
        }
    }

    /**
     * 删除饰品模块缓存
     * @return \Illuminate\Http\JsonResponse
     */
    public function delCache()
    {
        $params = $this->request->all();
        $key = $params['key'] ?? '';

        if ($key) {
            //删除模块下指定缓存
            Cache::tags(self::CACHE_TAG)->forget($key);

            //删除缓存并发锁
            Redis::DEL($key);
        } else {
            //删除模块下缓存
            Cache::tags(self::CACHE_TAG)->flush();
        }

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 房间详情
     * @param $rollId
     * @return array
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function rollInfo($rollId)
    {
        $rollId = $rollId ?? 0; //房间id
        if (!$rollId) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 0));
        }

        //检测房间是否存在
        $info = $this->checkRoll($rollId);

        $data =  RollRepository::mergeRollInfo($info, $this->userId);
        //200 返回
        return response_success(['data' => $data]);
    }

    /**
     * 房间饰品奖池列表
     * @param $rollId
     * @return array
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function rollItemList($rollId)
    {
        $rollId = $rollId ?? 0; //房间id
        $params = $this->request->all();

        if (!$rollId) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 0));
        }

        //分页, 限制每页最大不超过系统指定数量
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        //获取房间饰品
        $where = ['roll_id' => $rollId];
        $query = RollItem::where($where);
        $total = $query->count();
        $rollItemRes = $query->select('id', 'item_id', 'status','price')->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get()->toArray();

        $itemList = [];
        $itemAmount = 0;
        if (false === empty($rollItemRes)) {
            $itemIds = [];
            foreach ($rollItemRes as $key => $value) {
                $itemIds[] = $value['item_id'];
            }

            //获取所有饰品详情
            $itemInfoArr = ItemsRepository::getALLItemInfo($itemIds);

            foreach ($rollItemRes as $key => $value) {
                //因为房间中可能会有多个相同的饰品,用不了whereIn,所以只能循环中一个个查
                if (false === empty($itemInfoArr[$value['item_id']])) {
                    $itemInfo['roll_item_id'] = $value['id'];
                    $itemInfo['roll_item_status'] = $value['status'];

                    $itemInfo['item_id'] = $itemInfoArr[$value['item_id']]['item_id'];
                    $itemInfo['item'] =  $itemInfoArr[$value['item_id']]['items'];
                    $itemInfo['item']['price'] =  $value['price'] ?? 0;

                    $itemList[] =  $itemInfo;
                    $itemAmount += $value['price'] ?? 0;
                }
            }
        }
        return [
            'paging' => [
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'total' => $total
            ],
            //'extra' => [
            //    'item_amount' => $itemAmount,
            //    'item_num' => $total,
            //],
            'data' => $itemList
        ];
    }

    /**
     * 修复房间统计信息
     * @return bool
     */
    public function rollStatistic()
    {
        Log::info('修复房间统计信息开始');

        $params = $this->request->all();
        $rollIds = $params['roll_ids'] ?? '';
        if(!$rollIds){
            return '参数错误';
        }

        $rollIds = explode(',', $rollIds);

        $okIds = [];
        foreach ($rollIds as $key => $value) {
            //获取房间饰品总数数,总价值
            $itemRes = RollItem::getItemNumAndAmount($value);

            //获取房间参与人数
            $playersCount = RollPlayer::getRollPlayerNum($value);

            DB::connection('db_roll')->beginTransaction();
            try {

                //修改roll表
                $updateArr = ['item_amount'=>$itemRes['item_amount'], 'item_num'=>$itemRes['item_num'], 'players_count'=>$playersCount];
                Roll::where('id', $value)->update($updateArr);

                DB::connection('db_roll')->commit();

                $okIds[] = $value;

            } catch (Exception $e) {
                DB::connection('db_roll')->rollBack();

                //记错误日志
                Log::error('修复房间统计信息异常: ', ['error' => $e]);

                return '修复房间统计信息异常';
            }
        }

        Log::info('修复房间统计信息结束');

        return '执行完成'.json_encode($okIds);
    }

    /**
     * 修复房间奖池信息
     * @return bool
     */
    public function rollItemEdit()
    {
        Log::info('修复房间奖池信息开始');

        $params = $this->request->all();
        $rollIds = $params['roll_ids'] ?? '';
        if(!$rollIds){
            return '参数错误';
        }

        $rollIds = explode(',', $rollIds);

        $okIds = [];
        foreach ($rollIds as $key => $value) {
            //获取已经存在添加操作的sn
            $where = ['roll_id' => $value, 'type' => RollLog::TYPE_ADD];
            $snRes = RollLog::where($where)->pluck('id')->toArray();

            //查找房间相关饰品信息
            $itemRes = ItemsRepository::getUserInventoryHistoryItem($value);

            if (false === empty($itemRes)) {

                DB::connection('db_roll')->beginTransaction();
                try {
                    $statusTag = 0;

                    foreach ($itemRes['items'] as $itemsKey => $itemsValue) {
                        if (!in_array($itemsKey, $snRes)) {

                            //增加roll_log表
                            RollLog::insert([
                                'id'=>$itemsKey,
                                'roll_id' => $value,
                                'type' => RollLog::TYPE_ADD,
                                'item_status' => RollLog::STATUS_OK
                            ]);

                            //增加roll_item饰品
                            RollItem::insert($itemsValue);

                            //房间状态转为进行中
                            $statusTag = Roll::STATUS_NORMAL;
                        }
                    }

                    //修改饰品最大价值标记
                    RollItem::updateIsMax($value);

                    //修改roll表
                    $updateArr = ['item_amount'=>$itemRes['item_amount'], 'item_num'=>$itemRes['item_num']];
                    if($statusTag){
                        $updateArr = array_merge(['status'=>Roll::STATUS_NORMAL], $updateArr);
                    }
                    Roll::where('id', $value)->update($updateArr);

                    DB::connection('db_roll')->commit();

                    $okIds[] = $value;

                } catch (Exception $e) {
                    DB::connection('db_roll')->rollBack();

                    //记错误日志
                    Log::error('修复房间奖池信息异常: ', ['error' => $e]);

                    return '修复房间奖池信息异常';
                }
            }
        }

        Log::info('修复房间奖池信息结束');

        return '执行完成'.json_encode($okIds);
    }

    /**
     * 系统号饰品转移到房间(有可能海波那边的房间关联的饰品已经不存在了)
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function rollItemShift()
    {
        Log::info('系统号饰品转移到房间开始');

        $params = $this->request->all();
        $rollId = $params['rollId'] ?? 0;
        $rollId = (int)$rollId;
        $systemUserId = $params['system_user_id'] ?? 0;
        $systemUserId = (int)$systemUserId;
        $itemId = $params['item_id'] ?? 0;
        $itemId = (int)$itemId;
        if(!$rollId && !$systemUserId && !$itemId){
            return '参数错误';
        }



        DB::connection('db_roll')->beginTransaction();
        try {
            //插入logs
            $logArr = [
                'roll_id' => $rollId,
                'type' => RollLog::TYPE_ADD,
                'roll_time' => Carbon::now(),
            ];
            $res = RollLog::create($logArr);

            //组织sn信息
            $details = [['user_id'=>$systemUserId, 'item'=>[['item_id'=>$itemId,'count'=>1]]]];
            $curlArr = ['roll_id'=>$rollId, 'sn'=>$res->id,'details' => $details];

            //插入log_data信息
            $dataArr = [
                'roll_log_id' => $res->id,
                'roll_id' => $rollId,
                'data' => json_encode($curlArr),
            ];
            RollLogData::create($dataArr);

            //调用饰品api接口
            RollService::apiItem('/item/roll/add', $curlArr);

            DB::connection('db_roll')->commit();

        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('系统号饰品转移到房间异常: ', ['error' => $e]);

            return '系统号饰品转移到房间异常';
        }

        Log::info('系统号饰品转移到房间完成');

        return '系统号饰品转移到房间完成';
    }

    /**
     * 修复饰品转移正常,但是log状态没有改变的房间
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function updateRollLog($rollLogId)
    {
        $rollLogId = (int)$rollLogId; //房间id
        if (!$rollLogId) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 0));
        }
        $res = RollLog::where(['id'=>$rollLogId])->update(['item_status'=>RollLog::STATUS_OK]);
        //200 返回
        return response_success(['data' => $res]);
    }

    /**
     * 单个房间回调补发
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function itemRepairOne($rollId)
    {
        $rollId = (int)$rollId; //房间id
        if (!$rollId) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 0));
        }
        $this->itemCallbackRepairOne($rollId);
    }

    /**
     * API饰品操作完成回调通知批量补发(有可能海波那边的队列已经不存在了)
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function itemCallbackRepair()
    {
        Log::info('API饰品回调补发开始');

        //获取需要补发的数据
        $rows = RollLog::getRepairList();

        if ($rows) {

            //获取饰品api参数记录
            $rollLogIds = [];
            foreach ($rows as $key => $value) {
                $rollLogIds[] = $value['id'];
            }
            $logData = RollLogData::getLogDataList($rollLogIds);

            foreach ($rows as $k => $minRes) {

                $curlArr = ['sn' => $minRes['id'], 'roll_id' => $minRes['roll_id']];

                //如果有饰品api参数记录直接使用做参数
                if(false === empty($logData[$minRes['id']])){
                    $curlArr = json_decode($logData[$minRes['id']], true);
                }

                //`type`  '开奖方式(1手动,2自动,3饰品入奖池,4饰品出奖池,5自动开奖返还房主)',
                $type = $minRes['type'];
                if ($type === RollLog::TYPE_HAND || $type === RollLog::TYPE_AUTO) { //发奖
                    $apiUrl = '/item/roll/transfer';
                } elseif ($type === RollLog::TYPE_ADD) { //添加饰品
                    $apiUrl = '/item/roll/add';
                } else { //删除饰品
                    $apiUrl = '/item/roll/back';
                }

                //退回饰品给房主  (全部成功在掉API饰品接口处理用户饰品)
                RollService::apiItem($apiUrl, $curlArr, RollService::API_TYPE_REPAIR);

                Log::info('本次补发完成', ['data'=>$curlArr]);
            }
        } else {
            Log::info('API饰品回调补发结束, 本次无需补发');
            return 'API饰品回调补发结束, 本次无需补发';
        }

        Log::info('API饰品回调补发完成');

        return 'API饰品回调补发完成';
    }




    /**
     * API饰品操作完成回调通知单个补发(有可能海波那边的队列已经不存在了)
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function itemCallbackRepairOne($rollId)
    {
        Log::info('API饰品回调单个补发开始');

        //获取需要补发的数据
        $rows = RollLog::getRepairList($rollId);

        if ($rows) {

            //获取饰品api参数记录
            $rollLogIds = [];
            foreach ($rows as $key => $value) {
                $rollLogIds[] = $value['id'];
            }
            $logData = RollLogData::getLogDataList($rollLogIds);


            foreach ($rows as $k => $minRes) {

                $curlArr = ['sn' => $minRes['id'], 'roll_id' => $minRes['roll_id']];

                //如果有饰品api参数记录直接使用做参数
                if(false === empty($logData[$minRes['id']])){
                    $curlArr = json_decode($logData[$minRes['id']], true);
                }

                //`type`  '开奖方式(1手动,2自动,3饰品入奖池,4饰品出奖池,5自动开奖返还房主)',
                $type = $minRes['type'];
                if ($type === RollLog::TYPE_HAND || $type === RollLog::TYPE_AUTO) { //发奖
                    $apiUrl = '/item/roll/transfer';
                } elseif ($type === RollLog::TYPE_ADD) { //添加饰品
                    $apiUrl = '/item/roll/add';
                } else { //删除饰品
                    $apiUrl = '/item/roll/back';
                }

                //退回饰品给房主  (全部成功在掉API饰品接口处理用户饰品)
                RollService::apiItem($apiUrl, $curlArr, RollService::API_TYPE_REPAIR);

                Log::info('本次补发完成', ['data'=>$curlArr]);
            }
        } else {
            Log::info('API饰品回调单个补发结束, 本次无需补发');
            return true;
        }

        Log::info('API饰品回调单个补发完成');

        return true;
    }

    /**
     * 检测房间饰品转移奖池状态
     * @param $rollId
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function checkRollLog($rollId)
    {
        //检测这个房间是否所有饰品转移到奖池都已经成功
        $logTag = RollLog::countTypeAddNum($rollId);
        if($logTag){
            $this->itemCallbackRepairOne($rollId);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 76));
        }
    }

    /**
     * API饰品操作完成回调通知
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function itemCallback()
    {
        Log::info('API饰品操作完成回调通知开始');
        $params = $this->request->all();
        $rollLogId = $params['roll_log_id'] ?? 0;
        Log::info('参数', ['params' => $params]);
        if (!$rollLogId) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 0));
        }

        $rollLogRes = RollLog::where('id', $rollLogId)->first();
        if (!$rollLogRes) {
            //204 返回 查不到情况下有可能是旧数据没有
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 47));
        }

        //更改log状态
        if ($rollLogRes['item_status'] === RollLog::STATUS_NO) {
            $tag = RollLog::where('id', $rollLogId)->update(['item_status' => RollLog::STATUS_OK, 'callback_time' => Carbon::now()]);
            if (!$tag) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 48));
            }
            Log::info('API饰品操作完成回调通知:状态更改完成');
        }else{
            Log::info('API饰品操作完成回调通知:重复无需处理');
        }

        Log::info('API饰品操作完成回调通知完成');

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 从 roll 中删除饰品(管理后台用)
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function delItemAdmin()
    {
        Log::info('从 roll 中删除饰品(管理后台用)开始');

        $params = $this->request->all();
        $rollId = $params['roll_id'] ?? 0;//房间id
        $rollItemIds = $params['roll_item_ids'] ?? [];//饰品id数组
        Log::info('参数', ['params' => $params]);
        //检测是否勾选饰品
        if (true === empty($rollItemIds)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 21));
        }

        //检测房间是否存在
        $this->checkRoll($rollId);

        //检测这个房间是否所有饰品转移到奖池都已经成功
        $this->checkRollLog($rollId);

        DB::connection('db_roll')->beginTransaction();
        try {
            //退回饰品给房主
            $curlArr = $this->itemCllbakUserHand($rollId, $rollItemIds, RollLog::TYPE_DEL, 'hand');

            DB::connection('db_roll')->commit();
        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('增加饰品到Roll奖池异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 44));
        }

        //退回饰品给房主  (全部成功在掉API饰品接口处理用户饰品)
        RollService::apiItem('/item/roll/back', $curlArr);

        Log::info('从 roll 中删除饰品(管理后台用)结束');
        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 从 roll 中删除饰品
     * @param $rollId
     * @param $rollItemId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function delItem($rollId, $rollItemId)
    {
        Log::info('从 roll 中删除饰品开始');
        $rollId = $rollId ?? 0; //房间id
        $rollItemId = $rollItemId ?? 0; //饰品id数组
        $rollItemIds = [$rollItemId]; //饰品id数组
        Log::info('参数', ['rollId' => $rollId, 'userId' => $this->userId,'rollItemIds' => $rollItemIds]);

        //检测是否勾选饰品
        if (true === empty($rollItemIds)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 21));
        }

        //检测房间是否存在
        $info = $this->checkRoll($rollId);

        //检测是否房主
        if ($this->userId !== $info['owner_user_id']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 22));
        }

        //检测结束时间到不可再加减饰品
        switch ($info['status']) {
            case Roll::STATUS_END:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
                break; //结束
            case Roll::STATUS_FREEZE:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 12));
                break; //检测是否冻结
            case Roll::STATUS_FAILURE:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 13));
                break; //检测是否失效
            default:
                break;
        }

        //检测参与人超过10人不允许在删除饰品
        if ($info['players_count'] > self::PLAYERS_COUNT_MAX) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 45));
        }

        //检测这个房间是否所有饰品转移到奖池都已经成功
        $this->checkRollLog($rollId);

        //控制并发
        $delItemLockCacheKey = sprintf($this->delItemLockCacheKey, $rollId);
        //Redis::DEL($delItemLockCacheKey);die;
        if (!Redis::SETNX($delItemLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($delItemLockCacheKey, 3);


        DB::connection('db_roll')->beginTransaction();
        try {
            //退回饰品给房主
            $curlArr = $this->itemCllbakUserHand($rollId, $rollItemIds, RollLog::TYPE_DEL, 'hand');

            DB::connection('db_roll')->commit();
        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('增加饰品到Roll奖池异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 44));
        }

        //退回饰品给房主  (全部成功在掉API饰品接口处理用户饰品)
        RollService::apiItem('/item/roll/back', $curlArr);

        Log::info('从 roll 中删除饰品结束');

        //删除缓存并发锁
        Redis::DEL($delItemLockCacheKey);

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 增加饰品到 roll 奖池
     * @param $rollId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function addItem($rollId)
    {
        Log::info('增加饰品到Roll奖池开奖开始');
        $rollId = $rollId ?? 0; //房间id
        $rollId = (int)$rollId;
        $params = $this->request->all();
        $payPass = $params['pay_password'] ?? ''; //交易密码
        $itemIds = $params['item_ids'] ?? []; //饰品id数组
        Log::info('参数', ['rollId' => $rollId, 'userId' => $this->userId,'params' => $params]);

        //检测交易密码
        if (!$payPass) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 39));
        }

        //检测是否勾选饰品
        if (true === empty($itemIds)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 21));
        }



        //判断是否超出输入次数 24小时10次
        $cacheKey = sprintf($this->payCacheKey, $this->userId);
        $lockcacheKey = sprintf($this->payLockCacheKey, $this->userId);
        //Cache::tags(self::CACHE_TAG)->forget($cacheKey);
        //Cache::tags(self::CACHE_TAG)->forget($lockcacheKey);die;
        $num = Cache::tags(self::CACHE_TAG)->get($cacheKey, 1);
        $lockTime = Cache::tags(self::CACHE_TAG)->get($lockcacheKey, 0);
        if ($lockTime) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 41));
        }

        //到达上线记录锁定时间
        if ($num == self::PWDERROR_NUM && !$lockTime) {
            $time = time() + 86400;
            $expiresAt = Carbon::createFromTimestamp($time);
            $time = (array)$expiresAt;
            Cache::tags(self::CACHE_TAG)->put($lockcacheKey, $time['date'], $expiresAt);
        }

        //检测是否是三无用户,如果是提示强制绑定邮箱并验证
        RollService::apiSSO();

        //检测交易密码是否正确
        $oldPayPass = UserProfile:: getValue($this->userId, 'pay_pass');
        if (!$oldPayPass) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 42));
        }

        //累计错误次数
        $pwdTag =  UserProfile::userTradePassword($oldPayPass, $payPass) ;
        if (!$pwdTag) {
            Cache::tags(self::CACHE_TAG)->put($cacheKey, $num + 1, 1440);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 40));
        }


        //检测房间是否存在
        $info = $this->checkRoll($rollId);

        //检测是否房主
        if ($this->userId !== $info['owner_user_id']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 22));
        }

        //检测结束时间到不可再加减饰品
        switch ($info['status']) {
            case Roll::STATUS_END:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
                break; //结束
            case Roll::STATUS_FREEZE:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 12));
                break; //检测是否冻结
            case Roll::STATUS_FAILURE:
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 13));
                break; //检测是否失效
            default:
                break;
        }

        //检测奖品池的个数限制
        $rollItemCount = RollItem::where('roll_id', $rollId)->count();
        if ($rollItemCount + count($itemIds) > self::ITEM_MAX_NUM) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 43));
        }

        //饰品相关数据初始
        $curlArr = ['roll_id' => $rollId, 'details' => [['user_id' => $this->userId, 'item' => []]]];

        //检测用户背包饰品数量是否够
        $itemCount = UserInventory::getGroupByItemNum( $this->userId, $itemIds);
        if (true === empty($itemCount)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 77));
        }

        //比对每一中饰品库存是否够
        $inventoryItemArr = [];
        foreach ($itemCount as $key => $value) {
            $inventoryItemArr[$value['item_id']] = (int)$value['count'];
        }

        $tmpItemArr = array_count_values($itemIds);
        foreach ($tmpItemArr as $key => $value) {
            if(true === empty($inventoryItemArr[$key])){
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 30), ['items'=>$key]);
            }else{
                if(array_key_exists($key, $inventoryItemArr) && $inventoryItemArr[$key] < $value){
                    throw new UnprocessableEntityHttpException(ecode(self::ERR, 30), ['items'=>$key]);
                }
            }
        }

        //检测饰品是否是房主的
        $addArr = [];
        $addItemAmount = 0;
        $tmp = [];
        foreach ($itemIds as $key => $value) {
            $tmp[] = ['item_id' => (int)$value, 'count' => 1];

            //获取饰品价格
            $price = EconomySteamItem::getValue($value, 'price');
            $price *= 100;
            $addItemAmount += $price;

            //组织批量插入数据
            $addArr[] = [
                'roll_id' => $rollId,
                'item_id' => $value,
                'price' => $price,
            ];

        }
        $curlArr['details'][0]['item'] = $tmp;

        //检测这个房间是否所有饰品转移到奖池都已经成功
        $this->checkRollLog($rollId);

        //控制并发
        $addItemLockCacheKey = sprintf($this->addItemLockCacheKey, $rollId);
        if (!Redis::SETNX($addItemLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($addItemLockCacheKey, 3);

        //添加饰品
        DB::connection('db_roll')->beginTransaction();


        try {
            //记录饰品日志
            $rollLogRes = RollLog::addLog($rollId, RollLog::TYPE_ADD);
            $curlArr['sn'] = (int)$rollLogRes->id;

            //饰品加入奖池
            if (false === empty($addArr)) {
                RollItem::insert($addArr);
            }

            //修改房间状态为1正常,并增加饰品总数饰品总价值
            $itemAmount = $addItemAmount + $info['item_amount'] * 100;
            $updateArr = [
                'status' => Roll::STATUS_NORMAL,
                'item_amount' => $itemAmount,
                'item_num' => count($itemIds) + $info['item_num'],
            ];
            Roll::where('id', $rollId)->update($updateArr);

            //更新饰品最大价值标记
            RollItem::updateIsMax($rollId);

            // @ todo
            DB::connection('db_roll')->commit();


        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('增加饰品到Roll奖池异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 44));
        }


        //房主背包减饰品  (全部成功在掉API饰品接口处理用户饰品)
        RollService::apiItem('/item/roll/add', $curlArr);

        //安全检测(总金额超出限制,发邮件提醒) @tudo
        $rollTotalValueLimit = RollConfig::getValue('roll_total_value_limit');
        if ($rollTotalValueLimit && format_price($itemAmount)  > $rollTotalValueLimit) {
            $this->rollService->sendItemEmail($rollId);
        }

        Log::info('增加饰品到Roll奖池开奖结束');

        //删除缓存并发锁
        Redis::DEL($addItemLockCacheKey);

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);

    }

    /**
     * 手动ROLL开奖
     * @param $rollId
     * @return array
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function play($rollId)
    {
        $rollId = $rollId ?? 0; //房间id
        $params = $this->request->all();
        $winnerNum = $params['winner_num'] ?? 0; //中奖人数
        $rollItemIds = $params['roll_item_ids'] ?? []; //饰品id数组

        //检测中奖人
        if (!$winnerNum) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 20));
        }

        //检测是否超过限制
        if ($winnerNum > self::ROLL_PAY_USERNUM) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 53));
        }

        //检测是否勾选饰品
        if (true === empty($rollItemIds)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 21));
        }

        //获取房间详情
        $info = $this->checkRoll($rollId);

        switch ($info['status']) {
            case Roll::STATUS_FREEZE: //检测是否冻结
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 12));
                break;
            case Roll::STATUS_FAILURE: //检测是否失效
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 13));
                break;
            case Roll::STATUS_END: //检测是否结束
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
                break;
            default:
                break;
        }

        //检测进房时间是否到
        if (time() < strtotime($info['start'])) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 67));
        }

        //检测是否是房主
        if ($this->userId !== $info['owner_user_id']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 22));
        }

        //检测填写的人数是否大于参与会员
        if ($info['players_count'] > 0 && $winnerNum > $info['players_count']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 23));
        }

        //获取未中奖人数
        $noWinnerNum = RollPlayer::getNoWinnerNum($rollId);
        //检测填写的人数是否大于未中奖人数
        if ($winnerNum > $noWinnerNum) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 24));
        }

        //检测房间是否还有未roll的饰品
        $unsentItems = RollItem::getUnsentItemIds($rollId);
        if (true === empty($unsentItems)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 29));
        }

        //获取饰品ids
        $itemRes = RollItem::getUnsentItemIdsInfo($rollId, $rollItemIds);
        if (true === empty($itemRes)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 30));
        }

        $itemIds = [];
        foreach ($itemRes as $key => $value) {
            $itemIds[] = $value['item_id'];
        }

        //检测是否房间存在勾选的饰品
        $diffion = array_diff($itemIds, $unsentItems);
        if (false === empty($diffion)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 30), ['items'=>$diffion]);
        }

        //检测ROLL出的饰品数必须是中奖人数的倍数
        if ((count($itemIds) % $winnerNum) !== 0) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 27));
        }

        //检测这个房间是否所有饰品转移到奖池都已经成功
        $this->checkRollLog($rollId);

        Log::info('手动Roll开奖开始', ['rollId' => $rollId, 'userId' => $this->userId, 'params' => $params]);

        //计算每个人可获得的饰品数
        $userGetItemNum = count($itemIds) / $winnerNum;

        //获取未中奖用户ids
        $users = RollPlayer::getNoWinnerUserIds($rollId);

        Log::info(
            '手动ROLL房间号'.$rollId.'准备',
            [
                '饰品总数' => count($itemIds),
                '中奖人数' => count($winnerNum),
                '计算每个人可获得的饰品数' => $userGetItemNum
            ]
        );


        //控制并发
        $rollPayLockCacheKey = sprintf($this->rollPayLockCacheKey, $rollId);
        //Redis::DEL($delItemLockCacheKey);die;
        if (!Redis::SETNX($rollPayLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($rollPayLockCacheKey, 3);

        DB::connection('db_roll')->beginTransaction();
        DB::connection('db_vpgame')->beginTransaction();

        try {
            $addLogArr = []; //开奖日志
            $userGetItemLog = '';
            $winnerIds = []; //本次中奖用户id数组
            $rollPlayerIds = []; //参与者信息id
            $rollPlayerId = 0;
            $curlArr = ['roll_id' => (int)$rollId, 'details' => []];
            for ($i = 0; $i < $winnerNum; $i++) {
                //随机获取一个中奖用户,修改中奖会员中奖状态
                $playerUserId = $this->getRandUser($rollId, $users, $i);
                //api饰品数据
                $curlArr['details'][$i] = [
                    'user_id' => $playerUserId,
                    'item' => [],
                ];

                //获取roll_player_id
                $rollPlayerId = RollPlayer::getPlayerId($rollId, $playerUserId);
                $rollPlayerIds[] = $rollPlayerId;

                //for循环每个中奖饰品数,给中奖用户增加饰品
                $rollItem = 0;
                $rollItemId = 0;
                $itemAmount = 0;
                for ($n = 0; $n < $userGetItemNum; $n++) {
                    //随机抽一个勾选的饰品
                    $key = array_rand($itemRes);
                    $rollItem = $itemRes[$key]['item_id'] ?? 0;
                    $itemAmount += $itemRes[$key]['price'] ?? 0;
                    $rollItemId = $itemRes[$key]['id'] ?? 0;

                    if ($rollItem) {
                        //api饰品数据
                        $curlArr['details'][$i]['item'][] = ['item_id' => (int)$rollItem, 'count' => 1];

                        //修改饰品状态
                        RollItem::editRollItemId($rollItemId, RollItem::ROLL_HAND);

                        //添加会员中奖记录到中奖表
                        RollPlayerItem::addPlayerItem($rollPlayerId, $rollId, $playerUserId, $rollItem);

                        //给Roll房主增加经验值(创建Roll经验值 50)
                        //ExpManage::addExp($rollUser->roll_onwer_id, ExpValue::roll_item_event, $rollUser->roll_id);

                        $userGetItemLog .= '用户'.$playerUserId.'获得饰品'.$rollItem.';';
                    }

                    if (false === empty($itemRes[$key])) {
                        unset($itemRes[$key]);
                    }
                }

                //中奖用户饰品总价值累加
                $itemAmount *= 100;
                RollPlayer::where('roll_id', $rollId)->where('player_user_id', $playerUserId)->increment('item_amount', $itemAmount);

                //发送站内信提醒 @todu 信息注意语言问题
                $this->rollService->sendMsg($playerUserId, trans('messages.101012'), $rollId);

                $winnerIds[] = $playerUserId;
            }

            //记录开奖日志
            $logRes = RollLog::addLog($rollId, RollLog::TYPE_HAND);

            //修改开奖日志关联记录
            RollPlayer::updateRollLogId($logRes->id, $rollPlayerIds);

            Log::info('手动ROLL房间号'.$rollId.'执行', ['中奖情况' => $userGetItemLog]);

            //roll后续操作
            $this->afterRoll($rollId);

            //用户背包加饰品 (全部成功在掉API饰品接口处理用户饰品)
            $curlArr['sn'] = (int)$logRes->id;

            DB::connection('db_roll')->commit();
            DB::connection('db_vpgame')->commit();


        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();
            DB::connection('db_vpgame')->rollBack();

            //记错误日志
            Log::error('手动Roll开奖异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 32));
        }

        //用户背包加饰品 (全部成功在掉API饰品接口处理用户饰品)
        RollService::apiItem('/item/roll/transfer', $curlArr);

        //调用方法返回中奖本次中奖详情
        $rows = $this->playersService->getHandRollUser($rollId, $winnerIds);

        //删除缓存并发锁
        Redis::DEL($rollPayLockCacheKey);

        Log::info('手动Roll开奖结束');

        return ['data' => $rows];

    }


    /**
     * roll后续操作
     * @param $rollId
     */
    private function afterRoll($rollId, $type = 'hand')
    {
        //检测饰品是否发放完成
        $unsentNum = RollItem::where('roll_id', $rollId)->where('status', RollItem::ITEM_UNSENT)->count();

        Log::info('ROLL后续操作', ['unsentNum' => $unsentNum, 'type' => $type]);
        if (!$unsentNum) {
            //如果是自动ROLL
            if ($type === 'auto') {
                //改变roll房间活动状态为2已结束,同时更改排序为0,删除推荐标签
                Roll::where('id', $rollId)->update(['sort_weight' => 0, 'status' => Roll::STATUS_END, 'is_top' => Roll::TOP_NO]);
            } else {
                //改变roll房间活动状态为5未发布
                Roll::where('id', $rollId)->update(['status' => Roll::STATUS_NOT_RELEASE]);
            }
        }
    }

    /**
     * 随机获取一个中奖用户
     * @param $users
     * @return mixed
     */
    private function randUser($users)
    {
        if (count($users) > 1) {
            $randomKeys = array_rand($users);
            $playerUserId = $users[$randomKeys];
        } else {
            $playerUserId = $users[0];
        }

        return $playerUserId;
    }


    /**
     * 随机获取中奖会员
     * @param $rollId
     * @param $users
     * @param $i
     * @return mixed
     */
    private function getRandUser($rollId, $users, $i)
    {
        $playerRes = RollPlayer::getRollIdRandUser($rollId, $users, $i);

        //修改用户中奖状态
        $arr = [
            'is_winner' => 1,
            'win_time' => Carbon::now(),
            'player_client' => $this->client,
        ];
        RollPlayer::where('id', $playerRes[0]['id'])->update($arr);

        return $playerRes[0]['player_user_id'];
    }

    /**
     * 随机获取一个中奖用户,修改中奖会员中奖状态
     * @param $rollId
     * @param $users
     * @return mixed
     */
    private function getAutoRandUser($rollId, $users)
    {
        $playerUserId = $this->randUser($users);

        //修改用户中奖状态
        $arr = [
            'is_winner' => 1,
            'win_time' => Carbon::now(),
            'player_client' => $this->client,
        ];
        RollPlayer::where('roll_id', $rollId)->where('player_user_id', $playerUserId)->update($arr);

        return $playerUserId;
    }

    /**
     * 创建房间
     * @return Request
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function create()
    {
        Log::info('创建房间开始');

        //检测是否绑定steam
        //检测是否绑定steam交易链接
        $this->checkSteamOK();

        //参数检测
        $params = $this->request->all();
        $params = $this->checkParams($params);

        //增加系统账号不可roll(防止系统号被盗号,转移饰品)
        $systemStatus = User::isSystem($this->userId);
        if ($systemStatus) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 72));
        }

        Log::info('参数:', ['userId' => $this->userId, 'params' => $params]);

        //获取用户昵称
        $ownerRst = User::select('nickname', 'avatar')->where('id', $this->userId)->first();
        $ownerNickname = $ownerRst['nickname'] ?? '';
        $ownerAvatar = $ownerRst['avatar'] ?? '';

        DB::connection('db_roll')->beginTransaction();
        try {
            //添加房间 状态 5未发布
            $addArr = [
                'owner_user_id' => $this->userId,
                'owner_nickname' => $ownerNickname,
                'avatar' => $ownerAvatar,
                'max_players_count' => $params['max_players_count'],
                'status' => Roll::STATUS_NOT_RELEASE,
                'start' => Carbon::createFromTimestamp($params['start']),
                'end' => Carbon::createFromTimestamp($params['end']),
                'password' => $params['password'],
                'description' => $params['description'],
                'create_client' => $this->client,
                'is_private' => $params['is_private'],
            ];
            $result = Roll::create($addArr);
            Log::info('添加房间完成');

            if ($result->id) {
                $params['id'] = $result->id;

                //添加房间扩展信息
                $extendArr = [
                    'roll_id' => $result->id,
                    'lottery_type' => $params['lottery_type'],
                    'player_min_level' => $params['player_min_level'],
                ];
                RollExtend::create($extendArr);

                //添加房间tag附加设置,添加个人信息
                $this->rollService->addRollTag($result->id, $params);

                Log::info('添加房间附加信息完成,房间id:'. $result->id);
            }

            DB::connection('db_roll')->commit();

            //安全检测(每天创建房间超出限制,发邮件提醒)
            $this->rollService->sendRollEmail($result->id, $this->userId, $ownerNickname);
        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('创建房间异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 32));
        }

        Log::info('创建房间结束');

        //200 返回
        return response_success(['data' => $params]);
    }

    /**
     * 编辑房间
     * @param $rollId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function edit($rollId)
    {
        Log::info('编辑房间开始');

        $rollId = $rollId ?? 0; //房间id
        $params = $this->request->all();

        Log::info('参数', ['userId' => $this->userId, 'rollId' => $rollId, 'params' => $params]);

        //获取房间详情
        $info = Roll::where('id', $rollId)->first();
        if (!$info) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 10));
        }
        $info = $info->toArray();

        //检测是否房主
        if ($this->userId !== $info['owner_user_id']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 22));
        }

        //A、创建未添加饰品，可以编辑房间的所有信息,包括饰品加减
        //B、未到进房时间，可以编辑房间的所有信息,包括饰品加减
        //C、进房时间到但房间未到结束时间，不可编辑进房时间,不可改房间密码模式,不可改用户等级限制,不可改为私密ROLL,不可改为APP专享,不可更改开奖机制,不可以编辑参与人数
        //D、房间结束，不能编辑,包括饰品加减
        if ($info['status'] === Roll::STATUS_END) { //结束
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
        } elseif ($info['status'] === Roll::STATUS_NOT_RELEASE) { //未发布[刚创建没饰品]
            $endTime = strtotime($info['end']);
            if ($endTime < time()) { //结束到
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
            }

            $startTime = strtotime($info['start']);
            if ($startTime > time()) { //未到进房时间
                $params = $this->checkParams($params);
            } else { //进房时间到但房间未到结束时间
                $this->checkEditRoll($params, $info, $rollId);

                //检测参数
                $params = $this->checkParams($params, 1);
            }
        } elseif ($info['status'] === Roll::STATUS_FREEZE) { //冻结中
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 12));
        } elseif ($info['status'] === Roll::STATUS_FAILURE) { //已失效
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 13));
        } else { //正常
            $endTime = strtotime($info['end']);
            if ($endTime < time()) { //结束到
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 11));
            }

            $startTime = strtotime($info['start']);
            if ($startTime > time()) { //未到进房时间
                $params = $this->checkParams($params);
            } else { //进房时间到但房间未到结束时间
                $this->checkEditRoll($params, $info, $rollId);

                //检测参数
                $params = $this->checkParams($params, 1);
            }
        }

        DB::connection('db_roll')->beginTransaction();
        DB::connection('db_vpgame')->beginTransaction();
        try {
            //修改房间 状态 5未发布
            $addArr = [
                'owner_user_id' => $this->userId,
                'max_players_count' => $params['max_players_count'],
                'start' => Carbon::createFromTimestamp($params['start']),
                'end' => Carbon::createFromTimestamp($params['end']),
                'password' => $params['password'],
                'description' => $params['description'],
                'create_client' => $this->client,
                'is_private' => $params['is_private'],
            ];
            $result = Roll::where('id', $rollId)->update($addArr);
            Log::info('修改房间基本信息完成');

            if ($result) {
                //修改房间扩展信息
                $extendArr = [
                    'roll_id' => $rollId,
                    'lottery_type' => $params['lottery_type'],
                    'player_min_level' => $params['player_min_level'],
                ];
                RollExtend::where('roll_id', $rollId)->update($extendArr);

                //删除原有设置
                RollTag::where('roll_id', $rollId)->delete();
                RollExternalLink::where('roll_id', $rollId)->delete();

                //添加房间tag附加设置,添加个人信息
                $this->rollService->addRollTag($rollId, $params);

                Log::info('修改房间附加信息完成');
            }

            DB::connection('db_roll')->commit();
            DB::connection('db_vpgame')->commit();
        } catch (Exception $e) {
            DB::connection('db_roll')->rollBack();
            DB::connection('db_vpgame')->rollBack();

            //记错误日志
            Log::error('编辑房间异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 32));
        }

        Log::info('编辑房间结束');

        //200 返回
        return response_success(['data' => $params]);
    }

    /**
     * 进房时间到但房间未到结束时间,编辑房间时检测
     * @param $params
     * @param $info
     * @param $rollId
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function checkEditRoll($params, $info, $rollId)
    {

        $params['description'] = $params['description'] ?? ''; //描述
        $params['start'] = $params['start'] ?? ''; //进房时间
        $params['end'] = $params['end'] ?? ''; //结束时间
        $params['password'] = $params['password'] ?? ''; //房间密码
        $params['max_players_count'] = $params['max_players_count'] ?? 0; //参与人数上限制
        $params['lottery_type'] = $params['lottery_type'] ?? 0; //自动开奖类型
        $params['player_min_level'] = $params['player_min_level'] ?? 0; //限制参与者最低等级
        $params['is_private'] = $params['is_private'] ?? 0; //是否私密Roll
        $params['app_only'] = $params['app_only'] ?? 0; //是否APP专享
        $params['is_top'] = $params['is_top'] ?? 0; //是否推荐
        $params['links'] = $params['links'] ?? []; //个人信息1连接

        //不可编辑参与人数上限
        if ($params['max_players_count'] != $info['max_players_count']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 52));
        }

        //不可编辑进房时间
        if ($params['start'] != $info['start']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 14));
        }

        //检测时间是否超过最大限制 2037-12-31 23:59:59
        $maxTime = 2145887999;
        $msgDate = date('Y-m-d H:i:s', $maxTime);
        if(strtotime($params['end']) > $maxTime){
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 74),[],'',['time'=>$msgDate]);
        }

        //结束时间-进房时间 >= 1小时  &&  <= 72小时
        $checkTime = strtotime($params['end']) - strtotime($params['start']);
        if ($checkTime < 3600 || $checkTime > 259200) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 7));
        }

        //检测活动结束时间是否大于当前时间
        if (strtotime($params['end']) < time()) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 75));
        }


        //不可改房间密码模式,
        if ($params['password'] != $info['password']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 15));
        }

        //获取原扩展信息
        $extendRes = RollExtend::where('roll_id', $rollId)->first();
        $extendRes['lottery_type'] = $extendRes['lottery_type'] ?? 0;
        $extendRes['player_min_level'] = $extendRes['player_min_level'] ?? 0;

        //不可更改开奖机制
        if ($params['lottery_type'] != $extendRes['lottery_type']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 18));
        }

        //不可改用户等级限制
        if ($params['player_min_level'] != $extendRes['player_min_level']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 16));
        }

        //获取房间原tag附加设置
        $appOnly = RollTag::where('roll_id', $rollId)->where('tag', RollTag::TAG_APP_ROLL)->count();

        //不可改为私密ROLL
        if ($params['is_private'] != $info['is_private']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 17));
        }

        //不可改为APP专享
        if ($params['app_only'] != $appOnly) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 19));
        }
    }

    /**
     * 房间参数检测
     * @param $params
     * @return mixed
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function checkParams($params, $type = 0)
    {
        $params['description'] = $params['description'] ?? ''; //描述
        $params['start'] = $params['start'] ?? ''; //进房时间
        $params['end'] = $params['end'] ?? ''; //结束时间
        $params['password'] = $params['password'] ?? ''; //房间密码
        $params['max_players_count'] = $params['max_players_count'] ?? 0; //参与人数上限制
        $params['lottery_type'] = $params['lottery_type'] ?? 0; //自动开奖类型
        $params['player_min_level'] = $params['player_min_level'] ?? 0; //限制参与者最低等级
        $params['is_private'] = $params['is_private'] ?? 0; //是否私密Roll
        $params['app_only'] = $params['app_only'] ?? 0; //是否APP专享
        $params['is_top'] = $params['is_top'] ?? 0; //是否推荐
        $params['links'] = $params['links'] ?? []; //个人信息1连接

        //描述120字,默认 有一份爱在等你哦，来VP领取吧
        if (cn_strlen($params['description']) > 120) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 4));
        }

        if (!$params['start'] || !$params['end']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 5));
        }

        $params['start'] = strtotime($params['start']);
        $params['end'] = strtotime($params['end']);

        //检测时间是否超过最大限制 2037-12-31 23:59:59
        $maxTime = 2145887999;
        $msgDate = date('Y-m-d H:i:s', $maxTime);
        if($params['start'] > $maxTime){
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 74),[],'',['time'=>$msgDate]);
        }
        if($params['end'] > $maxTime){
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 74),[],'',['time'=>$msgDate]);
        }

//        //进房时间>当前时间
//        if (!$type && $params['start'] < time()) {
//            throw new UnprocessableEntityHttpException(ecode(self::ERR, 6));
//        }

        //结束时间-进房时间 >= 1小时  &&  <= 72小时
        $checkTime = $params['end'] - $params['start'];
        if ($checkTime < 3600 || $checkTime > 259200) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 7));
        }

        //检测活动结束时间是否大于当前时间
        if ($params['end'] < time()) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 75));
        }

        //密码 有, 6位数字密码
        if ($params['password']) {
            //if (!is_numeric($params['password'])) {
            //    throw new UnprocessableEntityHttpException(ecode(self::ERR, 8));
            //}

            //创建房间的密码格式不限制，限制长度20位
            if (cn_strlen($params['password']) > 20) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 8));
            }
        }

        //检测个人信息连接格式
        if (false === empty($params['links'])) {
            foreach ($params['links'] as $key => $value) {
                if ($value['link'] && !check_social_url($value['link'], $value['type'])) {
                    throw new UnprocessableEntityHttpException(ecode(self::ERR, 9));
                }
            }
        }

        return $params;
    }

    /**
     * 房间列表
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function list()
    {
        $params = $this->request->all();
        $params['filter'] = $params['filter'] ?? 'all';

        $res = [
            'paging'=>[
                'limit'=>10,
                'offset'=>0,
                'total'=>0,
            ],
            'data' => []
        ];

        if ($params['filter'] === 'joined') { //我参加
            if (!$this->userId) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 26));
            }
            $res = RollRepository::getRollList($this->userId, $params);
        } elseif ($params['filter'] === 'my') { //我创建的
            if (!$this->userId) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 26));
            }
            $res = RollRepository::getRollList($this->userId, $params);
        }else{
            //搜索全部时候 缓存1分钟
            $rollListCacheKey = sprintf($this->rollListCacheKey, json_encode($params));
            $dataTag = Cache::tags(self::CACHE_TAG)->has($rollListCacheKey);
            if (!$dataTag) {
                $res = RollRepository::getRollList($this->userId, $params);
                Cache::tags(self::CACHE_TAG)->put($rollListCacheKey, json_encode($res), 1);
            }else{
                $cache = Cache::tags(self::CACHE_TAG)->get($rollListCacheKey);
                $res = json_decode($cache, true);
            }
        }
        if(!$res){
            $res = [
                'paging'=>[
                    'limit'=>10,
                    'offset'=>0,
                    'total'=>0,
                ],
                'data' => []
            ];
        }

        return response_success($res);
    }

    /**
     * 自动Roll开奖(容错修复用)
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function autoPlayEdit()
    {
        $rollIds = $this->request->get('roll_ids','');
        if (!$rollIds) {
            return '参数错误';
        }
        $rollIds = explode(',', $rollIds);

        $allRolls = Roll::getEditRollIds($rollIds);

        if(!$allRolls){
            return '没有需要处理的';
        }

        Log::info('自动Roll开奖(容错修复用)');


        //筛选出结束时间后10分钟可以自行自动roll的房间1
        $str = '执行自动Roll开奖(容错修复用)完成: ';
        foreach ((array)$allRolls as $key => $value) {
            if (strtotime($value['end']) + 600 <= time()) {
                $okId = $this->execAutoRoll($value['id'], $value['status']);
                $str .= $okId.',';
            }
        }

        Log::info($str);
        Log::info('自动Roll开奖(容错修复用)结束');

        return $str;
    }

    /**
     * 自动Roll开奖(平均分)
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function autoPlay()
    {
        //筛选30分钟内未结束的房间,每次取100条
        $startTIme = Carbon::createFromTimestamp(time() - 1800);
        $endTime = Carbon::now();

        //筛选条件 状态(1正常,3冻结,4已失效,5未发布)
        $allRolls = Roll::getAutoRollIds($startTIme, $endTime);

        //测试用
        //$allRolls = Roll::select('id','status')->where('id', '=', 7237)->get()->toArray();
        if (true === empty($allRolls)) {
            return '没有需要处理的';
        }

        Log::info('自动Roll开奖开始', ['endTime' => $endTime]);


        //筛选出结束时间后10分钟可以自行自动roll的房间1
        $str = '执行自动roll完成: ';
        foreach ((array)$allRolls as $key => $value) {
            if (strtotime($value['end']) + 600 <= time()) {
                $okId = $this->execAutoRoll($value['id'], $value['status']);
                $str .= $okId.',';
            }
        }

        Log::info($str);
        Log::info('自动Roll开奖结束');

        return $str;
    }

    //执行自动Roll核心方法
    private function execAutoRoll($rollId, $rollSratus)
    {
        Log::info('自动ROLL房间号'.$rollId.'状态:'.$rollSratus);

        //检测这个房间是否所有饰品转移到奖池都已经成功
        $logTag = RollLog::countTypeAddNum($rollId);
        if($logTag){
            $this->itemCallbackRepairOne($rollId);
            Log::info('房间号'.$rollId.',饰品未转移到奖池,不在执行自动roll');
            return false;
        }

        DB::connection('db_roll')->beginTransaction();
        DB::connection('db_vpgame')->beginTransaction();
        try {
            if ($rollSratus === Roll::STATUS_NOT_RELEASE) {
                //检测是否未发布状态
                $this->afterRoll($rollId, 'auto');
            } else {
                //获取该活动的未发送的饰品
                $unsentItemData = RollItem::getUnsentItemInfo($rollId);

                //获取该活动的未中奖人数
                $unawardUserNum = RollPlayer::getNoWinnerUserIds($rollId);

                //是否有饰品
                if (false === empty($unsentItemData)) {
                    //是否有未中奖人
                    if (true === empty($unawardUserNum)) {
                        //饰品有的情况下,未中奖人无,返还饰品给房主
                        $cllbakCurlArr = $this->itemCllbakUser($rollId, $unsentItemData, RollLog::TYPE_RETURN, 'auto');
                    } else {
                        //检测状态是否已失效或者冻结
                        if ($rollSratus === Roll::STATUS_FAILURE || $rollSratus === Roll::STATUS_FREEZE) {
                            //剩余饰品返回房主背包
                            $cllbakCurlArr = $this->itemCllbakUser($rollId, $unsentItemData, RollLog::TYPE_RETURN, 'auto');
                        } elseif ($rollSratus === Roll::STATUS_NORMAL) {
                            //获取自动开奖设置
                            $lotteryType = RollExtend::getValue($rollId, 'lottery_type');

                            //检测自动开奖设置
                            if ($lotteryType === 1) {
                                //所有奖品随机roll给一名用户
                                $randUser = $this->randUser($unawardUserNum);
                                $unawardUserNum = [$randUser];
                            }

                            //计算中奖会员个数及单位中奖会员可得饰品数
                            $rollUserNum = number_avg(count($unsentItemData), count($unawardUserNum));
                            $userGetItemLog = '';

                            //api饰品数据
                            $curlArr = ['roll_id' => $rollId, 'details' => []];

                            $rollPlayerIds = []; //参与者信息id
                            $tmp = [];
                            if (false === empty($rollUserNum)) {
                                foreach ($rollUserNum as $key => $value) {
                                    //没有饰品直接不再执行
                                    if (true === empty($unsentItemData)) {
                                        continue;
                                    }

                                    //随机获取一个中奖用户,修改中奖会员中奖状态
                                    $playerUserId = $this->getRandUser($rollId, $unawardUserNum, $key);
                                    $tmpUser = ['user_id' => $playerUserId, 'item' => []];

                                    //获取roll_player_id
                                    $rollPlayerId = RollPlayer::getPlayerId($rollId, $playerUserId);
                                    $rollPlayerIds[] = $rollPlayerId;

                                    //计算该会员获取饰品个数
                                    $final_uinItemNum = $value;

                                    //循环中每个中奖会员可得到的饰品个数
                                    $itemAmount = 0;
                                    for ($n = 0; $n < $final_uinItemNum; $n++) {
                                        //随机抽一个勾选的饰品
                                        $key = array_rand($unsentItemData);
                                        $rollItem = $unsentItemData[$key]['item_id'] ?? 0;
                                        $price = $unsentItemData[$key]['price'] ?? 0;

                                        if ($rollItem) {
                                            $itemAmount += $price * 100;

                                            //api饰品数据
                                            $tmpUser['item'][] = ['item_id' => (int)$rollItem, 'count' => 1];

                                            //修改饰品状态
                                            RollItem::editRollItem($rollId, $rollItem, RollItem::ROLL_AUTO);

                                            //添加会员中奖记录到中奖表
                                            RollPlayerItem::addPlayerItem($rollPlayerId, $rollId, $playerUserId, $rollItem);

                                            $userGetItemLog .= '用户'.$playerUserId.'获得饰品'.$rollItem.';';
                                        }

                                        if (false === empty($unsentItemData[$key])) {
                                            unset($unsentItemData[$key]);
                                        }
                                    }

                                    //发送站内信提醒 @todu 信息注意语言问题
                                    $this->rollService->sendMsg($playerUserId, trans('messages.101012'), $rollId);
                                    $tmp[] = $tmpUser;

                                    //中奖用户饰品总价值累加
                                    if ($itemAmount > 0) {
                                        RollPlayer::where('roll_id', $rollId)
                                            ->where('player_user_id', $playerUserId)
                                            ->increment('item_amount', $itemAmount);
                                    }
                                }

                                //记录开奖日志
                                $logRes = RollLog::addLog($rollId, RollLog::TYPE_AUTO);

                                //修改开奖日志关联记录
                                RollPlayer::updateRollLogId($logRes->id, $rollPlayerIds);

                                //用户背包加饰品
                                $curlArr['sn'] = (int)$logRes->id;
                            }

                            $curlArr['details'] = $tmp;
                            Log::info(
                                '自动ROLL房间号'.$rollId.'准备',
                                [
                                    '饰品总数' => count($unsentItemData),
                                    '中奖人数' => count($unawardUserNum),
                                    '中奖情况' => $userGetItemLog
                                ]
                            );
                        }

                        //roll完饰品后，该活动设置为不显示
                        $this->afterRoll($rollId, 'auto');

                    }
                }else{
                    //如果没有饰品了都改为已结束
                    $this->afterRoll($rollId, 'auto');
                }
            }

            DB::connection('db_roll')->commit();
            DB::connection('db_vpgame')->commit();
        } catch (\Exception $e) {
            DB::connection('db_roll')->rollBack();
            DB::connection('db_vpgame')->rollBack();

            //记错误日志
            Log::error('自动Roll开奖异常: ', ['error' => $e]);
        }

        //API饰品接口发饰品给用户 (全部成功在掉API饰品接口处理用户饰品)
        if (false === empty($curlArr['sn'])) {
            RollService::apiItem('/item/roll/transfer', $curlArr);
        }

        //退回饰品给房主
        if (false === empty($cllbakCurlArr['sn'])) {
            RollService::apiItem('/item/roll/back', $cllbakCurlArr);
        }

        return $rollId;
    }


    /**
     * 饰品返还用户背包(用于手动)
     * @param $rollId
     * @param $rollItemIds
     * @param int $type
     * @param string $afterType
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     * @return array
     */
    private function itemCllbakUserHand($rollId, $rollItemIds, $type = 4, $afterType = 'hand')
    {
        //饰品相关数据初始
        $ownerUserId = Roll::getValue($rollId, 'owner_user_id');
        $curlArr = ['roll_id' => (int)$rollId, 'details' => [['user_id' => $ownerUserId, 'item' => []]]];

        $addItemAmount = 0;
        $tmp = [];
        foreach ($rollItemIds as $key => $value) {

            //检测饰品是否是房主的
            $rollItemRes = RollItem::where('id', $value)->where('status',RollItem::ITEM_UNSENT)->first();
            if (!$rollItemRes) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 30), ['roll_item_ids'=>$value]);
            }

            //获取饰品价格
            $addItemAmount += $rollItemRes['price'] * 100;

            $tmp[] = ['item_id' => (int)$rollItemRes['item_id'], 'count' => 1];
            //删除奖品池子
            RollItem::where('id', $value)->delete();
        }
        $curlArr['details'][0]['item'] = $tmp;

        //记录饰品日志
        $rollLogRes = RollLog::addLog($rollId, $type);
        $curlArr['sn'] = (int)$rollLogRes->id;

        //减少房间饰品总数饰品总价值
        $rollItemInfo = Roll::select('item_num', 'item_amount')->where('id', $rollId)->first();
        $itemAmount = $rollItemInfo['item_amount'] * 100;
        $updateArr = [
            'item_amount' => ($itemAmount - $addItemAmount) > 0 ? $itemAmount - $addItemAmount : 0,
            'item_num' => ($rollItemInfo['item_num'] - count($rollItemIds)) > 0 ? $rollItemInfo['item_num'] - count($rollItemIds) : 0 ,
        ];
        Roll::where('id', $rollId)->update($updateArr);

        //更新饰品最大价值标记
        RollItem::updateIsMax($rollId);

        //检测如果没有饰品了,修改状态为5未发布
        $this->afterRoll($rollId, $afterType);

        return $curlArr;
    }

    /**
     * 饰品返还用户背包(用于自动)
     * @param $rollId
     * @param $unsentItemData
     * @return bool
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function itemCllbakUser($rollId, $unsentItemData, $type = 4, $afterType = 'hand')
    {

        //获取房主用户id
        $fromUserId = Roll::getValue($rollId, 'owner_user_id');

        //饰品相关数据初始
        $curlArr = ['roll_id' => (int)$rollId, 'details' => []];

        $tmp = ['user_id' => $fromUserId, 'item' => []];
        $addItemAmount = 0;
        foreach ((array)$unsentItemData as $key => $value) {
            $tmp['item'][] = ['item_id' => (int)$value['item_id'], 'count' => 1];

            //检测饰品是否是房主的
            $rollItemId = RollItem::getMaxRollItemIds($rollId, $value['item_id']);

            if (!$rollItemId) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 30), ['items'=>$value['item_id']]);
            }

            //获取饰品价格
            $price = RollItem::getValue($rollItemId, 'price');
            $addItemAmount += $price * 100;

            //删除奖品池子
            RollItem::where('id', $rollItemId)->delete();
        }
        $curlArr['details'][] = $tmp;

        //记录饰品日志
        $rollLogRes = RollLog::addLog($rollId, $type);
        $curlArr['sn'] = (int)$rollLogRes->id;

        //减少房间饰品总数饰品总价值
        $rollItemInfo = Roll::select('item_num', 'item_amount')->where('id', $rollId)->first();
        $itemAmount = $rollItemInfo['item_amount'] * 100;
        $updateArr = [
            'item_amount' => ($itemAmount - $addItemAmount) > 0 ? $itemAmount - $addItemAmount : 0,
            'item_num' => ($rollItemInfo['item_num'] - count($unsentItemData)) > 0 ? $rollItemInfo['item_num'] - count($unsentItemData) : 0 ,
        ];
        Roll::where('id', $rollId)->update($updateArr);

        //更新饰品最大价值标记
        RollItem::updateIsMax($rollId);

        //检测如果没有饰品了,修改状态为5未发布
        $this->afterRoll($rollId, $afterType);

        return $curlArr;
    }

    /**
     * 检测是否做过赞,踩操作
     * @param $rollId
     * @param $type
     * @return int
     */
    private function checkLikeLog($rollId, $type)
    {
        $logTag = RollRepository::getLikeLogInfo($this->userId, $rollId, $type);
        return $logTag[0] ?? [];
    }

    /**
     * 赞
     * @param $rollId
     * @return mixed
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function addLike($rollId)
    {
        $rollId = $rollId ?? 0; //房间id

        //检测房间是否存在
        $this->checkRoll($rollId);


        //检测是否已做过赞
        $likeRes = $this->checkLikeLog($rollId, LikeLog::OP_LIKE);
        if (false === empty($likeRes) && !$likeRes['is_del']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 33));
        }

        //检测是否已做过踩
        $dislikeRes = $this->checkLikeLog($rollId, LikeLog::OP_DISLIKE);
        if (false === empty($dislikeRes) && !$dislikeRes['is_del']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 36));
        }

        //控制并发
        $addLikeLockCacheKey = sprintf($this->addLikeLockCacheKey, $rollId, $this->userId);
        if (!Redis::SETNX($addLikeLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($addLikeLockCacheKey, 3);

        //累加房间点赞数
        $likeId = Like::addLike($rollId, Like::SUBJECT_TYPE_ROLL);

        //增加用户点赞记录
        if (false === empty($likeRes) && $likeRes['is_del']) {
            LikeLog::where('id', $likeRes['id'])->update(['is_del' => 0]);
        } else {
            LikeLog::addLikeLog($likeId, LikeLog::OP_LIKE, $this->userId, $this->client);
        }

        //删除缓存并发锁
        Redis::DEL($addLikeLockCacheKey);

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }


    /**
     * 取消赞
     * @param $rollId
     * @return mixed
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function delLike($rollId)
    {
        $rollId = $rollId ?? 0; //房间id

        //检测房间是否存在
        $this->checkRoll($rollId);

        //检测是否已做过赞
        $LikeLogId = $this->checkLikeLog($rollId, LikeLog::OP_LIKE);
        if (true === empty($LikeLogId)) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 37));
        }

        //用户点赞记录更改为软删除
        LikeLog::where('id', $LikeLogId['id'])->update(['is_del' => LikeLog::IS_DEL_YES]);

        //减少房间点赞数
        Like::decrementCont($rollId, Like::SUBJECT_TYPE_ROLL);

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 踩
     * @param $rollId
     * @return mixed
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function addDislike($rollId)
    {
        $rollId = $rollId ?? 0; //房间id
        //检测房间是否存在
        $this->checkRoll($rollId);

        //检测是否已做过踩
        $dislikeRes = $this->checkLikeLog($rollId, LikeLog::OP_DISLIKE);
        if (false === empty($dislikeRes) && !$dislikeRes['is_del']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 35));
        }

        //检测是否已做过赞
        $likeRes = $this->checkLikeLog($rollId, LikeLog::OP_LIKE);
        if (false === empty($likeRes) && !$likeRes['is_del']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 34));
        }

        //控制并发
        $delLikeLockCacheKey = sprintf($this->delLikeLockCacheKey, $rollId, $this->userId);
        if (!Redis::SETNX($delLikeLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($delLikeLockCacheKey, 3);

        //累加房间踩数
        $likeId = Like::addLike($rollId, Like::SUBJECT_TYPE_ROLL, 'dislike_count');

        //增加用户踩记录
        if (false === empty($dislikeRes) && $dislikeRes['is_del']) {
            LikeLog::where('id', $dislikeRes['id'])->update(['is_del' => 0]);
        } else {
            LikeLog::addLikeLog($likeId, LikeLog::OP_DISLIKE, $this->userId, $this->client);
        }

        //删除缓存并发锁
        Redis::DEL($delLikeLockCacheKey);

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 取消踩
     * @param $rollId
     * @return mixed
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function delDislike($rollId)
    {
        $rollId = $rollId ?? 0; //房间id

        //检测房间是否存在
        $this->checkRoll($rollId);

        //检测是否已做过踩
        $LikeLogId = $this->checkLikeLog($rollId, LikeLog::OP_DISLIKE);
        if (true === empty($LikeLogId) && !$LikeLogId['is_del']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 38));
        }

        //用户点踩记录更改为软删除
        LikeLog::where('id', $LikeLogId['id'])->update(['is_del' => LikeLog::IS_DEL_YES]);

        //减少房间点踩数
        Like::decrementCont($rollId, Like::SUBJECT_TYPE_ROLL, 'dislike_count');

        //204 返回
        return response_success([], Response::HTTP_NO_CONTENT);
    }

    /**
     * 检测房间存在
     * @param $rollId
     * @return mixed
     */
    private function checkRoll($rollId)
    {
        $info = Roll::where('id', $rollId)->first();
        if (!$info) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 10));
        }
        $info = $info->toArray();

        return $info;
    }

    /**
     * 检测是否绑定steam与交易连接
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    private function checkSteamOK()
    {
        //检测是否绑定steam
        $tag = UserProfile::where('user_id', $this->userId)->where('steam_id', '!=', 0)->count();
        if (!$tag) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 1));
        }

        //检测是否绑定steam交易链接
        $tag = UserAttribute::where('user_id', $this->userId)->where('name', 'steam_trade_token')->count();
        if (!$tag) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 2));
        }
    }

    /**
     * 参与 roll
     * @param $rollId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnprocessableEntityHttpException
     */
    public function join($rollId)
    {
        Log::info('参与roll开始');
        $password = $this->request->get('password', '');

        //检测是否绑定steam
        $tag = UserProfile::where('user_id', $this->userId)->where('steam_id', '!=', 0)->count();
        if (!$tag) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 1));
        }

        //控制并发
        $addPayLockCacheKey = sprintf($this->addPayLockCacheKey, $rollId);
        if (!Redis::SETNX($addPayLockCacheKey, 'locked')) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 73));
        }
        Redis::EXPIRE($addPayLockCacheKey, 3);

        //检测房间存在
        $info =  $this->checkRoll($rollId);

        //检测是否房主 $tudo 取消注释
        if ($this->userId === $info['owner_user_id']) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 60));
        }

        //判断是否已经加入, 若已经加入则直接
        $playerTag = RollPlayer::where(['roll_id' => $rollId, 'player_user_id' => $this->userId])->count();
        if ($playerTag) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 61));
        }

        //判断是不是 app 专享
        $appOnly = RollTag::where(['roll_id' => $rollId, 'tag' => RollTag::TAG_APP_ROLL])->count();
        if ($appOnly) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 62));
        }

        //是否有等级限制
        $playerMinLevel = RollExtend::getValue($info['id'], 'player_min_level');
        if ($playerMinLevel > 0) {
            //获取用户等级
            $level = User::getValue($this->userId, 'level');
            if ($playerMinLevel > $level) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 66),[],'', ['level' => $playerMinLevel]);
            }
        }

        //如果是密码房间
        if ($info['password']) {
            //输入次数判断 24小时 6次
            $cacheKey = sprintf($this->rollCacheKey, $this->userId);
            $lockcacheKey = sprintf($this->rollLockCacheKey, $this->userId);

            //Cache::tags(self::CACHE_TAG)->forget($cacheKey);
            //Cache::tags(self::CACHE_TAG)->forget($lockcacheKey);die;
            $num = Cache::tags(self::CACHE_TAG)->get($cacheKey, 1);
            $lockTime = Cache::tags(self::CACHE_TAG)->get($lockcacheKey, 0);
            if ($lockTime) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 65));
            }

            //到达上线记录锁定时间
            if ($num == self::ROLL_PWDERROR_NUM && !$lockTime) {
                $time = time() + 86400;
                $expiresAt = Carbon::createFromTimestamp($time);
                $time = (array)$expiresAt;
                Cache::tags(self::CACHE_TAG)->put($lockcacheKey, $time['date'], $expiresAt);
            }

            //密码是否正确
            if ($password != $info['password']) {
                //记录错误次数
                Cache::tags(self::CACHE_TAG)->put($cacheKey, $num + 1, 1440);
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 64));
            }
        }

        //当前状态是否可以加入
        //if ($info['status'] === Roll::STATUS_NOT_RELEASE) { //未发布
        //    throw new UnprocessableEntityHttpException(ecode(self::ERR, 67));
        //} else
        if ($info['status'] === Roll::STATUS_END) { //结束
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 69));
        } elseif ($info['status'] === Roll::STATUS_FREEZE) { //冻结
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 70));
        } elseif ($info['status'] === Roll::STATUS_FAILURE) { //失效
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 71));
        }

        //检测进房时间是否到
        if (time() < strtotime($info['start'])) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 67));
        }

        //检测结束时间是否到
        if (time() > strtotime($info['end'])) {
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 69));
        }

        Log::info('参数', ['userId' => $this->userId, 'password' => $password]);

        DB::connection('db_roll')->beginTransaction();

        //查询时候加锁,控制并发
        $playersCount = Roll::getValue($rollId, 'players_count');
        try {
            //如果有人数限制
            if ($info['max_players_count'] > 0 && $info['max_players_count'] <= $playersCount) {
                throw new UnprocessableEntityHttpException(ecode(self::ERR, 63));
            }

            //增加参与用户记录
            $addArr = [
                'roll_id' => $rollId,
                'owner_user_id' => $info['owner_user_id'],
                'player_user_id' => $this->userId,
                'player_client' => $this->client,
            ];
            RollPlayer::create($addArr);

            //增加房间参与者总数
            Roll::where('id', $rollId)->increment('players_count');

            Log::info('参与roll结束');

            DB::connection('db_roll')->commit();

            //删除缓存并发锁
            Redis::DEL($addPayLockCacheKey);

            //204 返回
            return response_success([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::connection('db_roll')->rollBack();

            //记错误日志
            Log::error('参与roll异常: ', ['error' => $e]);
            throw new UnprocessableEntityHttpException(ecode(self::ERR, 32));
        }
    }
}
