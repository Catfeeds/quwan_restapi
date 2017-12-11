<?php

namespace App\Jobs;

use App\Services\XSService;
use Illuminate\Support\Facades\Log;

class XssJob extends Job{
    protected $data = '';
    //初始化接收参数
    public function __construct($data) {
        $this->data = $data;
    }
    //加载需要的服务,执行队列方法
    public function handle(XSService $XSService) {
        Log::error('队列开始=============', $this->data);
//        $XSService->jobEditIndex($this->data);
        $apiUrl = config('xs.api_url').'quwan/edit_index';
        Log::error('url:'.$apiUrl);
        $res = post_curl_content($apiUrl, $this->data);
        Log::error('返回:'.$res);
    }
}