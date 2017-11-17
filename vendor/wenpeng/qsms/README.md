# 简述
腾讯云Qcloud短信服务第三方SDK

# 用法
```
use Wenpeng\Qsms\Client;
$client = new Client($appID, $appKey);
```

## 单发短信
```
use Wenpeng\Qsms\Request\Single;
$sms = new Signle($client, 0);
```

### 单发普通短信
```
use Wenpeng\Qsms\Request\Single;
$sms = new Signle($client, 0);
$sms->target('18800001111', '86');
$response = $sms->normal('这是测试短信内容');
```
```
{ 
    "result": "0", //0表示成功(计费依据)，非0表示失败
    "errmsg": "", //result非0时的具体错误信息
    "ext": "some msg", //用户的session内容，腾讯server回包中会原样返回
    "sid": "xxxxxxx", //标识本次发送id
    "fee": 1 //短信计费的条数
}
```

### 单发模板短信
```
$sms->target('18800001111', '86');
// 短信正文模板编号 1000, 短信正文参数 ['123456', 30]
$response = $sms->template(1000, ['123456', 30]);
```
```
{ 
    "result": "0", //0表示成功(计费依据)，非0表示失败
    "errmsg": "", //result非0时的具体错误信息
    "ext": "some msg", //用户的session内容，腾讯server回包中会原样返回
    "sid": "xxxxxxx", //标识本次发送id
    "fee": 1 //短信计费的条数
}
```