<?php
/**
 * Author: Wen Peng
 * Email: imwwp@outlook.com
 * Create: 2016/10/9 下午4:51
 */

namespace Wenpeng\Qsms;

use Exception;
use Wenpeng\Curl\Curl;

class Client
{
    private $appID;
    private $appKey;

    public function __construct($appID, $appKey)
    {
        $this->appID = $appID;
        $this->appKey = $appKey;
    }

    public function appID()
    {
        return $this->appID;
    }

    public function appKey()
    {
        return $this->appKey;
    }

    public function post($url, $params)
    {
        $curl = new Curl();
        $curl->post(json_encode($params));
        $curl->url($url .'?sdkappid='. $this->appID .'&random='. microtime(true));

        if ($curl->error()) {
            throw new Exception($curl->message());
        }

        $data = json_decode($curl->data(), true);
        if ($data === false) {
            throw new Exception('响应数据异常');
        }
        return $data;
    }
}