<?php

namespace App\Repository;

use App\Models\EconomySteamItem;
use App\Models\RollOwnerRanking;
use App\Models\RollPlayerItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlayersRepository extends BaseRepository
{

    const ERR = 901;

    protected $rollOwnerRanking;
    protected $userModel;

    public function __construct(RollOwnerRanking $rollOwnerRanking, User $userModel)
    {
        parent::__construct();
        $this->rollOwnerRanking = $rollOwnerRanking;
        $this->userModel = $userModel;
    }

    /**
     * 获取获奖者饰品相关信息
     *
     * @param $rollPlayerIds
     * @return array
     */
    public function getRollPlayerItemBasicInfo($rollPlayerIds)
    {
        $lang = app('translator')->getLocale();
        $rows = RollPlayerItem::select('id', 'roll_player_id', 'roll_id', 'player_user_id', 'item_id')
            ->whereIn('roll_player_id', $rollPlayerIds)
            ->get()
            ->toArray();

        $itemIds = array_column($rows, 'item_id');

        $itemRows = EconomySteamItem::whereIn('id', $itemIds)
            ->get()
            ->toArray();

        $items = [];
        if ($itemRows) {
            $tmp = [];
            foreach ($itemRows as $k => $v) {

                $tmp['name'] = $lang === 'cn' ? $v['name'] : $v['market_hash_name'];
                $tmp['image'] = image_cdn_path($v['image_url'], 'item');
                $tmp['quality_internal_name'] = $v['quality_name'];
                $tmp['rarity_internal_name'] = $v['rarity_name'];
                $tmp['id'] = $v['id'];
                $tmp['quality'] = $v['quality'];
                $tmp['rarity'] = $v['rarity'];
                $tmp['price'] = $v['price'];
                $tmp['gold'] = $v['gold'];
                $tmp['price_dollar'] = $v['price_dollar'];
                $tmp['rarity_name'] = $v['rarity_name'];
                $tmp['quality_name'] = $v['quality_name'];
                $tmp['slot_name'] = $v['slot_name'];
                $tmp['game_name'] = $v['game_name'];
                $tmp['appid'] = $v['appid'];

                $items[$v['id']] = $tmp;
            }
        }

        $rollPlayerItems = [];
        if ($rows) {
            foreach ($rows as $k => $v) {
                $rollPlayerItems[$v['roll_player_id']][] = $items[$v['item_id']] ?? [];
            }
        }

        return $rollPlayerItems;
    }

    /**
     * 首页排行榜
     *
     * @return array
     */
    public function getTopRank()
    {
        $rows = $this->rollOwnerRanking->getCurrentRank();

        $data = [];
        if ($rows) {
            $users = $this->userModel->getUserBasicInfo(array_column($rows, 'owner_user_id'));

            foreach ($rows as $k => $v) {
                if(false === empty($users[$v['owner_user_id']])){
                    $users[$v['owner_user_id']]['nickname'] = $v['owner_nickname'];
                    $users[$v['owner_user_id']]['avatar'] = $v['avatar'];

                    $data[] = [
                        'total_amount' => $v['roll_amount'],
                        'player' => $users[$v['owner_user_id']],
                    ];
                }
            }
        }

        return $data;
    }
}
