<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Attractions;
use App\Models\CidMap;
use App\Models\Destination;
use App\Models\DestinationJoin;
use App\Models\Message;
use App\Models\Img;
use App\Models\Route;
use App\Models\User;

class MessageService
{
    const MESSAGE_STATUS_0 = 0; //0删除
    const MESSAGE_STATUS_1 = 1; //1有效

    protected $destinationJoin;
    protected $destination;
    protected $img;
    protected $attractions;
    protected $cidMap;
    protected $route;
    protected $message;
    protected $user;

    public function __construct(
        User $user,
        Message $message,
        Route $route,
        CidMap $cidMap,
        DestinationJoin $destinationJoin,
        Destination $destination,
        Img $img,
        Attractions $attractions
    )
    {


        $this->user = $user;
        $this->message = $message;
        $this->route = $route;
        $this->cidMap = $cidMap;
        $this->destinationJoin = $destinationJoin;
        $this->destination = $destination;
        $this->img = $img;
        $this->attractions = $attractions;

    }

    public function setAllRead($userId)
    {
        $this->message::where('user_id','=',$userId)->update(['message_read'=>$this->message::MESSAGE_READ_1]);
        $this->user::where('user_id','=',$userId)->update(['user_msg_num'=>0]);
    }

    public function getInfoData($messageId, $userId)
    {
        $info = $this->message::getInfo($messageId);
        if (true === empty($info)) {
            throw new UnprocessableEntityHttpException(850004);
        }

        //更改消息未读状态
        if((int)$info['message_read'] === $this->message::MESSAGE_READ_0){
            if((int)$info['message_type'] === $this->message::MESSAGE_TYPE_2){
                $this->message::where('message_id','=',$messageId)->update(['message_read'=>$this->message::MESSAGE_READ_1]);
            }

            //减少自己的未读消息数
            $tag = $this->user::where('user_id','=',$userId)->where('user_msg_num','>',0)->count();
            if ($tag) {
                $this->user::where('user_id','=',$userId)->decrement('user_msg_num');
            }
        }

        return $info;
    }

    public function getListData($data)
    {
        $limit = $data['limit'] ?? 12; //每页显示数
        $offset = $data['offset'] ?? 1; //页码
        $offset = ($offset - 1) * $limit;


        $query = $this->message::select('message_id', 'message_read', 'message_title','message_comment');
        $query->where('user_id','=', $data['user_id'])
            ->where('message_status','=',self::MESSAGE_STATUS_1)
            ->where('message_type','=', Message::MESSAGE_TYPE_2);
        $query->orWhere(function ($query) {
            $query->where('user_id','=', 0)
                ->where('message_status','=',self::MESSAGE_STATUS_1)
                ->where('message_type','=', Message::MESSAGE_TYPE_1);
        });

        $query->orderBy('message_id', 'DESC');

        $result['_count'] = $query->count();
        $result['data'] = $query->skip($offset)->take($limit)->get()->toArray();
        if (false === empty($result['data'])) {
            foreach ($result['data'] as $key => &$value) {
                $value['message_comment'] = cn_substr($value['message_comment'], 0, 20);

            }
        }

        return $result;

    }
}