<?php

namespace App\Http\Controllers\V1;



use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\FormUploader;
use Qiniu\Storage\UploadManager;

/**
 * Class TestController
 * @package App\Http\Controllers\V1
 */
class TestController extends Controller
{

    protected $tokenService;
    protected $request;

    public function __construct(TokenService $tokenService,Request $request) {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;
    }

    public function xs()
    {


        try
        {
            // 创建 XS 对象，关于项目配置文件请参见官网
            $xs = new \XS('article');
            $index = $xs->index;
            //var_dump($xs);
//            var_dump($index);

            // 创建文档对象
            $doc = new \XSDocument();

            //$doc->setFields($data);

            $res = Article::get()->toArray();
            foreach ($res as $key => $value) {

                $data = array(
                    'id' => $value['id'], // 此字段为主键，必须指定
                    'author' => $value['author'], // 此字段为主键，必须指定
                    'title' => $value['title'], // 此字段为主键，必须指定
                    'content' => $value['content'], // 此字段为主键，必须指定
                    'post_time' => $value['post_time'], // 此字段为主键，必须指定
                    'chrono' => time()
                );
                var_dump($data);die;
                $doc->setFields($data);

            }


            $index->add($doc);

            var_dump($res[0]);

        }
        catch (\XSException $e)
        {
            echo $e;               // 直接输出异常描述
            if (defined('DEBUG'))  // 如果是 DEBUG 模式，则输出堆栈情况
            {
                echo "\n" . $e->getTraceAsString() . "\n";
            }
        }

    }

    public function qiniu()
    {
        //QINIU_ACCESS_KEY=r4Puq7v6E1MrD8q2SZpRrtX3_exPMOHPuVRgquAT
        //QINIU_SECRET_KEY=iqMsNBgQ5_nLkdpSNwjxieDLvno8YMcH1PXHTYGz
        //QINIU_BUCKET=weiba-ms
        //QINIU_DOMAIN=http://7xk9pc.com2.z0.glb.qiniucdn.com/

        $accessKey = 'r4Puq7v6E1MrD8q2SZpRrtX3_exPMOHPuVRgquAT';
        $secretKey = 'iqMsNBgQ5_nLkdpSNwjxieDLvno8YMcH1PXHTYGz';
        $testAuth = new Auth($accessKey, $secretKey);

        $name = '1.jpg';
        $fileName = base_path().'/public/1.jpg';
        $bucketName = 'weiba-ms';
        $token = $testAuth->uploadToken($bucketName, $name);

        $upManager = new UploadManager();
        $res = $upManager->putFile($token, $name, $fileName);
        var_dump($res);

    }

    public function sendSms()
    {

//        用法
//
//        use Wenpeng\Qsms\Client;
//        $client = new Client($appID, $appKey);
//        单发短信
//
//        use Wenpeng\Qsms\Request\Single;
//        $sms = new Signle($client, 0);
//        单发普通短信
//        use Wenpeng\Qsms\Request\Single;
//        $sms = new Signle($client, 0);
//        $sms->target('18800001111', '86');
//        $response = $sms->normal('这是测试短信内容');
//        {
//        "result": "0", //0表示成功(计费依据)，非0表示失败
//        "errmsg": "", //result非0时的具体错误信息
//        "ext": "some msg", //用户的session内容，腾讯server回包中会原样返回
//        "sid": "xxxxxxx", //标识本次发送id
//        "fee": 1 //短信计费的条数
//        }

//        单发模板短信
//        $sms->target('18800001111', '86');
//        // 短信正文模板编号 1000, 短信正文参数 ['123456', 30]
//        $response = $sms->template(1000, ['123456', 30]);
//        {
//        "result": "0", //0表示成功(计费依据)，非0表示失败
//        "errmsg": "", //result非0时的具体错误信息
//        "ext": "some msg", //用户的session内容，腾讯server回包中会原样返回
//        "sid": "xxxxxxx", //标识本次发送id
//        "fee": 1 //短信计费的条数
//        }

//        return response_success(['token' => $token]);
    }

    public function login()
    {
        //生成 token
        $userId = 12345;
        $token = $this->tokenService->createToken($userId, 'web');

        return response_success(['token' => $token]);
    }



    public function user()
    {
        return response_success(['userId' => $this->userId]);
    }

    public function logout()
    {
        $bearerToken = $this->request->server->getHeaders()['AUTHORIZATION'] ?? '';
        $claims = $this->tokenService->getJwtClaims($bearerToken);
        $this->tokenService->revokeToken($claims['jti']);

        return response_success(['msg' => '退出成功']);
    }

}
