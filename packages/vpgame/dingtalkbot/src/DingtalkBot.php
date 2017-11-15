<?php

namespace Vpgame\DingtalkBot;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DingtalkBot
{
    private $config;

    public function __construct()
    {
        $this->config = Config::get('dingtalkbot');
    }

    public function send($message, $to = 'default')
    {
        $serverIP = $_SERVER['SERVER_ADDR'] ?? 'Unknown IP';

        if ($message instanceof \Exception) {
            $message = 'Message: '.$message->getMessage().PHP_EOL.'File: '.$message->getFile().PHP_EOL.'Line: '.$message->getLine();
        }
        $message = '⚠️ '.Carbon::now()->toDateTimeString().PHP_EOL.'ServerIP: '.$serverIP.PHP_EOL.'Project: '.$this->config['project'].PHP_EOL.$message;
        $webhook = array_key_exists($to, $this->config['bot']) ? $this->config['bot'][$to] : $this->config['bot']['default'];
        $content = json_encode(['msgtype' => 'text','text' => array ('content' => $message)]);
        $res = $this->curl($webhook, $content);

        if ($res['statusCode'] !== 200) {
            Log::error('DingTalk Bot Error! 消息发送失败, 通知群:'.$to.' 消息:'.$message);
            return false;
        }
        return true;
    }

    private function curl($webhook, $content) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhook);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $return = [];
        $return['statusCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 500;
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $return['rawHeader'] = substr($response, 0, $headerSize);
        $return['rawBody'] = substr($response, $headerSize);
        $return['body'] = json_decode($return['rawBody'], true) ?: [];
        curl_close($ch);

        return $return;
    }
}
