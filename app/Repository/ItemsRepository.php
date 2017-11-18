<?php

namespace App\Repository;

use App\Models\EconomySteamItem;
use App\Models\UserInventory;
use App\Models\UserInventoryHistory;
use App\Models\UserInventoryTransferHistoryMap;
use Illuminate\Support\Facades\DB;

class ItemsRepository extends BaseRepository
{

    const ERR = 902;

    /**
     * 背包饰品查询
     *
     * @param $userId
     * @param $filter
     * @param $sort
     * @param $limit
     * @param $offset
     * @return array
     */
    public function getBackpackItemList($userId, $filter, $sort, $limit, $offset)
    {
        $lang = app('translator')->getLocale();

        $query = UserInventory::select(
            'user_inventory.id as user_inventory_id',
            'user_inventory.item_id'
            )
            ->leftJoin('steam_item', 'user_inventory.item_id', '=', 'steam_item.id')
            ->where('user_id', '=', $userId)
            ->where('status', '=', 'normal')
            //->where('steam_item.bet', '=', 'yes')
            ->where('steam_item.virtual', '=', 'no')
            ->where(function ($where) use ($filter, $lang) {
                //饰品类型筛选
                $filter['type'] = $filter['type'] ?? 0;
                if ($filter['type'] === EconomySteamItem::ITEM_APPID_PUBG || $filter['type'] === EconomySteamItem::ITEM_APPID_DOTA2 || $filter['type'] === EconomySteamItem::ITEM_APPID_CSGO) {
                    $where->where('user_inventory.appid', '=', $filter['type']);
                }

                //饰品名称筛选 @todo 用的是 like, 不知道会不会炸掉
                if ($filter['name']) {
                    if ($lang  === 'cn') {
                        $where->where('steam_item.name', 'like', '%'.$filter['name'].'%');
                    } else {
                        $where->where('steam_item.market_hash_name', 'like', '%'.$filter['name'].'%');
                    }
                }

                //dota专用稀有度,品质筛选
                if ($filter['quality']) {
                    $where->where('steam_item.quality', '=', $filter['quality']);
                }

                if ($filter['rarity']) {
                    $where->where('steam_item.rarity', '=', $filter['rarity']);
                }

                //csgo专用外观,品质筛选
                if ($filter['slot']) {
                    $where->where('steam_item.slot', '=', $filter['slot']);
                }

                if ($filter['rarity_internal_name']) {
                    $where->where('steam_item.rarity_internal_name', '=', $filter['rarity_internal_name']);
                }

                //根据饰品id筛选
                if (false === empty($filter['items'])) {
                    $where->whereIn('steam_item.id',  $filter['items']);
                }
            });


        //@todo order 不能使用匿名函数? 待确认
        if (in_array($sort['sortby'], ['price', 'create_time'], true) && in_array($sort['order'], ['asc', 'desc'], true)) {
            if ($sort['sortby'] === 'create_time') {
                $query->orderBy('user_inventory.'.$sort['sortby'], $sort['order']);
            } else {
                $query->orderBy('steam_item.'.$sort['sortby'], $sort['order']);
            }
        } else {
            //默认排序
            $query->orderBy('user_inventory.id', 'desc');
        }

        $total = $query->count();
        $data = $query->offset($offset)->limit($limit)->get()->toArray();
        //var_dump($data);die;

        //格式处理
        $itemList = [];
        if (false === empty($data)) {

            $itemIds = [];
            foreach ($data as $k => $v) {
                $itemIds[] = $v['item_id'];
            }

            //获取所有饰品详情
            $itemInfoArr = ItemsRepository::getALLItemInfo($itemIds);

            foreach ($data as $k => $v) {

                $item = [
                    'user_inventory_id' => $v['user_inventory_id'],
                    'item_id' => $v['item_id'],
                    'item' => new \stdClass(),
                ];

                //因为房间中可能会有多个相同的饰品,用不了whereIn,所以只能循环中一个个查
                if (false === empty($itemInfoArr[$v['item_id']])) {
                    $item['item'] =  $itemInfoArr[$v['item_id']]['items'];
                }

                $itemList[] = $item;
            }
        }

        return [
            'paging' => [
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'total' => $total
            ],
            'data' => $itemList
        ];
    }

    /**
     * 饰品详情
     * @param $itemId
     * @return array
     */
    public static function getItemInfo($itemId)
    {
        $lang = app('translator')->getLocale();

        $data = EconomySteamItem::where('steam_item.id',  $itemId)->first();


        //格式处理
        $itemList = [];
        if ($data) {
            $data = $data->toArray();

            $item = [
                'id' => $data['id'],
                'image' => image_cdn_path($data['image_url'], 'item'),
                'price' => $data['price'],
                'name' => $lang === 'cn' ? $data['name'] : $data['market_hash_name'],
                'quality' => $data['quality'],
                'quality_internal_name' => $data['quality_name'],
                'rarity' => $data['rarity'],
                'rarity_internal_name' => $data['rarity_name'],
                'slot' => $data['slot'],
                'slot_name' => $data['slot_name'],
            ];

            $itemList = [
                'user_inventory_id' => 0,
                'item_id' => $data['id'],
                'items' => $item,
            ];
        }

        return $itemList;
    }

    /**
     * 获取指定饰品详情
     * @param $itemIds
     * @return array
     */
    public static function getALLItemInfo($itemIds)
    {

        $lang = app('translator')->getLocale();
        $data = EconomySteamItem::whereIn('steam_item.id',  $itemIds)->get()->toArray();

        //格式处理
        $itemList = [];
        if (false === empty($data)) {

            foreach ($data as $key => $value) {
                $item = [
                    'id' => $value['id'],
                    'image' => image_cdn_path($value['image_url'], 'item'),
                    'price' => $value['price'],
                    'name' => $lang === 'cn' ? $value['name'] : $value['market_hash_name'],
                    'quality' => $value['quality'],
                    'quality_internal_name' => $value['quality_name'],
                    'rarity' => $value['rarity'],
                    'rarity_internal_name' =>  $value['rarity_name'] ,
                    'slot' => $value['slot'],
                    'slot_name' => $value['slot_name'],
                    'gold' => $value['gold'],
                    'appid' => $value['appid'],
                ];

                $itemList[$value['id']] = [
                    'user_inventory_id' => 0,
                    'item_id' => $value['id'],
                    'items' => $item,
                ];
            }
        }

        return $itemList;
    }

    /**
     * 查找房间相关饰品信息
     * @param $eventId
     * @return mixed
     */
    public static function getUserInventoryHistoryItem($eventId)
    {
        $itemRes = UserInventoryHistory::select(
                'user_inventory_history.id',
                't1.item_id',
                't2.price',
                't3.transfer_history_id',
                't4.sn')
            ->leftJoin('user_inventory_history_item AS t1', 't1.history_id', '=', 'user_inventory_history.id')
            ->leftJoin('steam_item AS t2', 't1.item_id', '=', 't2.id')
            ->leftJoin('user_inventory_transfer_history_map AS t3', 't3.user_history_id', '=', 'user_inventory_history.id')
            ->leftJoin('user_inventory_transfer_history AS t4', 't4.id', '=', 't3.transfer_history_id')
            ->where('user_inventory_history.event', '=', 'roll')
            ->where('user_inventory_history.event_id', '=', $eventId)
            ->where('user_inventory_history.user_id', '=', 3909144)
            ->where('user_inventory_history.type', '=', 'income')
            ->get()
            ->toArray();

        $arr = [];
        $itemNum = 0;
        $itemAmount = 0;
        $snArr = [];
        if (false === empty($itemRes)) {
            foreach ($itemRes as $key => $value) {
                $itemNum++;
                $itemAmount += $value['price'];

                $snArr[] = $value['sn'];
            }

            $snArr = array_unique($snArr);

            foreach ($snArr as $key => $value) {
                foreach ($itemRes as $keyB => $valueB) {
                    if($valueB['sn'] === $value){
                        $arr['items'][$value][] = [
                            'roll_id' => $eventId,
                            'item_id' => $valueB['item_id'],
                            'price'  => $valueB['price'],
                        ];
                    }
                }
            }

            $arr['item_num'] = $itemNum;
            $arr['item_amount'] = $itemAmount;
        }

        return $arr;
    }

    /**
     * 查找房间相关sn
     * @param $userHistoryId
     * @return mixed
     */
    public static function getRollSn($userHistoryId){
        $sn = UserInventoryTransferHistoryMap
            ::leftJoin('user_inventory_transfer_history as t1', 't1.id', '=', 'user_inventory_transfer_history_map.transfer_history_id')
            ->where('user_inventory_transfer_history_map.user_history_id', $userHistoryId)
            ->value('sn');
        return $sn;
    }
}
