<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use Qcloud\Sms\SmsSingleSender;

class SmsService
{
    public static function send($phone, $msg = '')
    {
        //准备必要参数
        $appid = config('sms.sms_appid');
        $appkey = config('sms.sms_appkey');

        $type = 4;
        switch ($type) {
            case 1: //购买提醒
                $msg = '【趣玩旅游】你购买了XXX，消费了XXX元';
                break;
            case 2: //用户确认订单
                $msg = '【趣玩旅游】用户XXX确认了他的订单，你有XXX元到账';
                break;
            case 3: //车友会给你结款
                $msg = '【趣玩旅游】车友会给你结款XXX元';
                break;
            case 4: //登录验证码
                $msg = '【趣玩旅游】你的登录验证码是XXXX，请勿告诉其他人';
                break;
            case 5: //节日来临
                $msg = '【趣玩旅游】你报名的节日XXX，将在2天后开始，请注意准备';
                break;

            default:
                break;
        }


        //单发短信
        $sender = new SmsSingleSender($appid, $appkey);

        //发送模板消息
        $templId = 58447;
        $params = ['123456', '3'];
        // 假设模板内容为：测试短信，{1}，{2}，{3}，上学。
        $result = $sender->sendWithParam('86', $phone, $templId, $params, '', '', '');

        //发送单条信息
        //$result = $sender->send(0, "86", $phone, $msg, "", "");

        $rsp = json_decode($result,true);
        if(true === empty($rsp)){
            throw new UnprocessableEntityHttpException(850010,[],'',['msg'=>'短信服务无返回信息']);
        }

        if ($rsp['errmsg'] !== 'ok') {
            throw new UnprocessableEntityHttpException(850010,[],'',['msg'=>$rsp['errmsg']]);
        }

        return $rsp;
    }
}