<?php

namespace App\Repository;

use App\Models\EconomySteamItem;
use App\Models\Like;
use App\Models\LikeLog;
use App\Models\Roll;
use App\Models\RollExtend;
use App\Models\RollExternalLink;
use App\Models\RollItem;
use App\Models\RollPlayer;
use App\Models\RollTag;
use App\Models\User;
use App\Models\UserAttribute;
use App\Models\UserTag;

class RollRepository extends BaseRepository
{

    const ERR = 901;

//    public $userTag = null;
//
//    public function __construct(UserTag $userTag)
//    {
//        parent::__construct();
//
//        $this->userTag = $userTag;
//    }

    /**
     * 房间列表
     * @param $userId
     * @param $params
     * @return array
     */
    public static function getRollList($userId, $params)
    {
        $limit = $params['limit'] ?? 12; //每页显示数
        $offset = $params['offset'] ?? 0; //页码

        $sortby = $params['sortby'] ?? ''; //排序字段, players_count热度, item_amount奖品价值, item_num奖品数量

        //排序字段, players_count热度, item_amount奖品价值, item_num奖品数量
        $sortbyArr = ['players_count','item_amount','item_num'];
        if(!in_array($sortby,$sortbyArr)){
            $sortby = '';
        }

        $order = $params['order'] ?? 'DESC'; //排序顺序, 升序,倒序
        if($order !== 'ASC'){
            $order = 'DESC';
        }

        $filter = $params['filter'] ?? 'all'; //过滤显示范围: all 全部/ joined 我参加的/ my 我创建的/ coming 即将开奖
        $filterArr = ['all','joined','my','coming'];
        if(!in_array($filter,$filterArr)){
            $filter = 'all';
        }

        $password = $params['password'] ?? ''; //筛选条件:是否需要密码 yes / no
        $passwordArr = ['yes','no'];
        if(!in_array($password,$passwordArr)){
            $password = '';
        }

        $rollId = $params['roll_id'] ?? 0; //筛选条件: roll_id
        $rollId = (int)$rollId; //筛选条件: roll_id
        $ownerNickname = $params['owner_nickname'] ?? ''; //筛选条件: 房主昵称

        //定义查询字段
        $query = Roll::select('roll.*');

//        全部房间 (where正常, 排序 推荐从大到小, 创建时间倒序)
//        即将开奖 (where正常, 排序 开奖时间倒序, 创建时间倒序)
//        我参加的 (排序 最新参加倒序, 创建时间倒序)
//        我创建的 (排序 最新创建倒序)
        $wheres = [];
        if ($filter === 'joined') { //我参加
            if ($userId) {



                $query = Roll::select(
                    'roll.*',
                    'roll_player.player_user_id',
                    'roll_player.is_winner',
                    'roll_player.win_time',
                    'roll_player.player_client',
                    'roll_player.created_at AS player_at'
                );

                $condition = array(array('column' => 'roll_player.player_user_id', 'value' => $userId, 'operator' => '='));
                $wheres = array_merge($condition, $wheres);

                //$query->orderBy('roll.end', 'ASC');
                $query->orderBy('roll_player.created_at', 'DESC');
            }
        } elseif ($filter === 'my') { //我创建的
            if ($userId) {
                $condition = array(array('column' => 'roll.owner_user_id', 'value' => $userId, 'operator' => '='));
                $wheres = array_merge($condition, $wheres);
            }

            //$query->orderBy('roll.end', 'ASC');
        } elseif ($filter === 'coming') { //即将开奖
            $condition = array(array('column' => 'roll.status', 'value' => Roll::STATUS_NORMAL, 'operator' => '='));
            $wheres = array_merge($condition, $wheres);

            $condition = array(array('column' => 'roll.is_private', 'value' => Roll::PRIVATE_NO, 'operator' => '='));
            $wheres = array_merge($condition, $wheres);

            $query->orderBy('roll.end', 'ASC');
        } else { //全部房间
            //1. 全部房间列表除新房间未添加饰品,无可roll饰品,冻结,无效,私密的以外都显示(包括进行中,活动结束的房间都显示)
            //2. 房间排序优先条件顺序为  推荐(权重倒序排)->进行中(活动结束时间倒序)->已结束(活动结束时间倒序)
            //3. 推荐在后台设置,默认为 1,  设置越大,排越前面
            //4. 推荐在后台设置时,检测如果是已结束房间,提示不可再设置推荐
            //5. 房间活动时间到,执行自动roll后,自动把结束房间的权重改为 0, 已达到已结束的房间排在后面

            $condition = array(array('column' => 'roll.is_private', 'value' => Roll::PRIVATE_NO, 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
            $condition = array(array('column' => 'roll.item_num', 'value' => 0, 'operator' => '>'));
            $wheres = array_merge($condition, $wheres);

            if ($sortby) {
                //全部房间的按热度排序、按价值排序、按数量排序不要把过期的房间排出来
                $query->whereIn('roll.status', [Roll::STATUS_NORMAL]);

                //按照指定规则排序
                $query->orderBy('roll.'.$sortby, $order);
            } else {
                $query->whereIn('roll.status', [Roll::STATUS_NORMAL, Roll::STATUS_END]);

                //默认权重排序
                $query->orderBy('roll.sort_weight', 'DESC');
                $query->orderBy('roll.end', 'ASC');
            }
        }

        if ($password) {
            if($password === 'no'){
                $query->where('roll.password', '');
            }else{
                $query->where('roll.password', '>', '');
            }
        }

        if ($rollId) {
            $condition = array(array('column' => 'roll.id', 'value' => $rollId, 'operator' => '='));
            $wheres = array_merge($condition, $wheres);
        }

        if ($ownerNickname) {
            $ownerNickname = trim($ownerNickname);
            $ownerNickname = htmlspecialchars(addslashes($ownerNickname));
            //echo($ownerNickname);die;
            $condition = array(array('column' => 'roll.owner_nickname', 'value' => '%' . $ownerNickname . '%', 'operator' => 'like'));
            $wheres = array_merge($condition, $wheres);
        }

        //载入查询条件
        $wheres = array_reverse($wheres);
        foreach ($wheres as $value) {
            $query->where($value['column'], $value['operator'], $value['value']);
        }

        $query->orderBy('roll.created_at', 'DESC');
        if ($filter === 'joined') { //我参加
            if ($userId) {
                $query->leftJoin('roll_player', 'roll_player.roll_id', '=', 'roll.id');
            }
        }

        if ($ownerNickname) {
            $result['_count'] = 500;
        }else{
            //如果是全部列表,避免慢sql直接返回10000条
            if ($filter === 'all'){
                $result['_count'] = 10000;
            }else{
                $result['_count'] = $query->count();
            }
        }


        $result['data'] = $query->skip($offset)->take($limit)->get()->toArray();
        if (false === empty($result['data'])) {
            $rollIds = [];
            $userIds = [];
            foreach ((array)$result['data'] as $key => &$value) {
                $rollIds[] = $value['id'];
                $userIds[] = $value['owner_user_id'];
            }
        }

        if (false === empty($result['data'])) {
            //获取房间app_roll标签
            $appRollIds = RollTag::whereIn('roll_id', $rollIds)->where('tag', RollTag::TAG_APP_ROLL)->pluck('tag','roll_id')->toArray();

            //获取房间最大价值饰品id
            $rollItemRes = RollItem::where(['is_max'=>RollItem::IS_MAX])->whereIn('roll_id', $rollIds)->pluck('item_id','roll_id')->toarray();


            //获取最大价值饰品, 图片, 饰品类型
            $itemInfo = [];
            $itemIds = array_unique(array_values($rollItemRes));
            if($itemIds){
                $itemRes =EconomySteamItem::select('image_url','id','appid')->whereIn('id', $itemIds)->get()->toArray();
                if (false === empty($itemRes)) {
                    foreach ($itemRes as $key => $value) {
                        $itemInfo[$value['id']] = $value;
                    }
                }
            }
            $itemListInfo = [];
            foreach ($rollItemRes as $key => $value) {
                $itemListInfo[$key] = $itemInfo[$value] ?? [];
            }

            //是否参与是否中奖
            if ($userId) {
                $playerRes = RollPlayer::whereIn('roll_id', $rollIds)
                    ->where('player_user_id', $userId)
                    ->pluck('is_winner','roll_id')
                    ->toArray();
            }

            //获取房主等级
            $userIds = array_unique($userIds);
            $levels = User::whereIn('id', $userIds)->pluck('level','id')->toArray();

            foreach ($result['data'] as $key => &$value) {
                $value['password'] = $value['password'] ? '******' : '';

                //饰品信息
                $value['image'] = $itemListInfo[$value['id']]['image_url'] ?? '';
                $value['appid'] = $itemListInfo[$value['id']]['appid'] ?? 0;
                $value['item_id'] = $itemListInfo[$value['id']]['id'] ?? 0;

                //是否参与,是否中奖
                $value['is_player'] = isset($playerRes[$value['id']]) ? 1 : 0;
                $value['is_winner'] = isset($playerRes[$value['id']]) && $playerRes[$value['id']] === 1 ?? 0;

                //是否app专享
                $value['app_roll'] = isset($appRollIds[$value['id']]) ? 1 : 0;

                //是否app专享
                $value['owner_user_level'] = $levels[$value['owner_user_id']] ?? 0;

                $value['roll_id'] = $value['id'];
                $value['is_winner'] = $value['is_winner'] ? 1 : 0;
                $value['is_top'] = $value['is_top'] ? 1 : 0;
                $value['is_private'] = $value['is_private'] ? 1 : 0;
            }
        }

        //var_dump($result['data']);

        return [
            'paging' => [
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'total' => $result['_count'],
            ],
            'data' => $result['data']
        ];
    }

    /**
     * 组织房间基本详情数据
     * @param array $info roll房间详情数组
     * @return array
     */
    public static function mergeRollInfo(array $info, $userId)
    {
        //检测是否房主
        $data = [];
        $data['is_owner'] = false;
        if ($userId && $userId === $info['owner_user_id']) {
            $data['is_owner'] = true;
        }else{
            $info['password'] = $info['password'] ? '******' : '';
        }

        //检测是否参加并中奖
        $isPlayer = false;
        $isWin = false;
        $playerInfo = RollPlayer::where(['roll_id' => $info['id'], 'player_user_id' => $userId])->first();
        if ($playerInfo) {
            $isPlayer = true;
            $isWin = $playerInfo['is_winner'] ? true : false ;
        }

        //按钮,文案,状态 初始化
        if (time()  < strtotime($info['start'])) { //进房时间未到
            $data['status_info'] = [
                'status'=> 1,
                'start_msg'=>  trans('messages.101010'),
                'button_msg'=> trans('messages.101011'),
                'button_status'=> false,
            ];

        } elseif (time() > strtotime($info['end'])){ //结束时间到
            $data['status_info'] = [
                'status'=> 5,
                'start_msg'=> trans('messages.101004'),
                'button_msg'=> trans('messages.101005'),
                'button_status'=> false,
            ];
        }else{
            $data['status_info'] = [
                'status'=> 2,
                'start_msg'=> trans('messages.101000'),
                'button_msg'=> trans('messages.101001'),
                'button_status'=> true,
            ];

            //检测已参加过
            if ($isPlayer) {
                $data['status_info']['button_msg'] =  trans('messages.101002');
                $data['status_info']['button_status'] = false;
            } else {
                //检测房间人数已满
                if ($info['max_players_count'] > 0 && $info['players_count'] >= $info['max_players_count']) {
                    $data['status_info']['button_msg'] =  trans('messages.101003');
                    $data['status_info']['button_status'] = false;
                }
            }
        }

        //房间可参与状态,文案,提示 (1待开放,2进行中,3进行中已参与,4进行中人数满,5已过期,6已冻结,7已失效)
        if ($info['status'] === Roll::STATUS_FREEZE) { //冻结
            $data['status_info']['status'] = 6;
            $data['status_info']['button_msg'] = trans('messages.101007');
            $data['status_info']['button_status'] = false;
        } elseif ($info['status'] === Roll::STATUS_FAILURE) { //失效
            $data['status_info']['status'] = 7;
            $data['status_info']['button_msg'] = trans('messages.101009');
            $data['status_info']['button_status'] = false;
        }


        $data['status_info']['is_player'] = $isPlayer;
        $data['status_info']['is_win'] = $isWin;

        //修复旧数据房主昵称问题
        if($info['owner_nickname'] === ''){
            $userRes = User::select('nickname', 'avatar')->where('id', $info['owner_user_id'])->first();
            if($userRes){
                Roll::where('id', $info['id'])->update(['owner_nickname' => $userRes['nickname'], 'avatar' => $userRes['avatar']]);
                $info['owner_nickname'] = $userRes['nickname'];
                $info['avatar'] = $userRes['avatar'];
            }
        }

        //获取房主头像昵称等级,头衔
        $data['owner']['owner_user_id'] = $info['owner_user_id'];
        $data['owner']['level'] = User::where('id', $info['owner_user_id'])->value('level');
        $data['owner']['nickname'] = $info['owner_nickname'];
        $data['owner']['avatar'] = $info['avatar'];
        $data['owner']['user_tag'] = UserTag::getTagName($info['owner_user_id']);

        //获取点赞数,踩数
        $data['like'] = ['like' => 0, 'dislike' => 0];
        $likeWhere = ['subject_type' => Like::SUBJECT_TYPE_ROLL, 'subject_id' => $info['id']];
        $likeInfo = Like::select('like_count', 'dislike_count')->where($likeWhere)->first();
        if (false === empty($likeInfo)) {
            $data['like'] = ['like' => $likeInfo['like_count'], 'dislike' => $likeInfo['dislike_count']];
        }

        //获取房间附加信息
        $extend = RollExtend::where('roll_id', $info['id'])->first();
        $appOnly = RollTag::where(['roll_id' => $info['id'], 'tag' => RollTag::TAG_APP_ROLL])->count();

        //活动开始倒计时
        $endTimestamp = strtotime($info['end']) - time();
        $data['end_imestamp'] = $endTimestamp > 0 ? $endTimestamp : 0;

        //房间基本信息
        $data['basic'] = [
            'password' =>  $info['password'],
            'start' =>  $info['start'],
            'end' =>  $info['end'],
            'start_timestamp' => strtotime($info['start'])*1000,
            'end_timestamp' =>  strtotime($info['end'])*1000,
            'description' =>  $info['description'],
            'player_min_level' =>  $extend->player_min_level ?? 0,
            'lottery_type' =>   $extend->lottery_type ?? 0,
            'app_only' =>  $appOnly,
            'is_private' =>  $info['is_private'],
            'max_players_count' =>   $info['max_players_count'],
            'status' =>   $info['status'],
        ];

        //饰品概况
        $data['item_info'] = [
            'item_amount' =>  $info['item_amount'],
            'item_num' =>  $info['item_num'],
        ];

        //房间个人信息连接
        $data['links'] = RollExternalLink::select('website AS type', 'link')->where('roll_id', $info['id'])->get()->toArray();

        //中奖人总数
        $winNum = RollPlayer::where(['roll_id' => $info['id'], 'is_winner' => RollPlayer::OK_WINNER])->count();
        $data['players_info'] = [
            'players_count' => $info['players_count'],
            'win_count' => $winNum,
        ];


        return $data;
    }

    /**
     * 获取房间点赞或点踩记录
     * @param $userId
     * @param $rollId
     * @param $type
     * @return mixed
     */
    public static function getLikeLogInfo($userId, $rollId, $type)
    {
        $logTag = LikeLog::select('like_log.*')
            ->where('like_log.user_id', $userId)
            ->where('like_log.operation', $type)
            ->where('like.subject_type', Like::SUBJECT_TYPE_ROLL)
            ->where('like.subject_id', $rollId)
            ->leftJoin('like', 'like.id', '=', 'like_log.like_id')
            ->get()
            ->toArray();

        return $logTag;
    }
}
