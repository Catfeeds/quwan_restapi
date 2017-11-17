<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 16:10
 */

namespace App\Services;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\FormUploader;
use Qiniu\Storage\UploadManager;
use App\Exceptions\UnprocessableEntityHttpException;

class QiNiuService
{
    /**
     * @param $fileName
     * @param $destinationPath
     * @return array
     */
    public static function uploadQiniu($fileName, $destinationPath): array
    {
        $bucketName = config('qiniu.bucketname');
        $accessKey = config('qiniu.accesskey');
        $secretKey = config('qiniu.secretkey');
        $qiniuUrl = config('qiniu.qiniuurl');
        $testAuth = new Auth($accessKey, $secretKey);

        $name = $fileName;
        $fileName = $destinationPath . $fileName;
        $token = $testAuth->uploadToken($bucketName, $name);

        $upManager = new UploadManager();
        $res = $upManager->putFile($token, $name, $fileName);
        if (true === empty($res[0]['key'])) {
            throw new UnprocessableEntityHttpException(850006);
        }

        //删除原文件
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        return array($qiniuUrl, $res);
    }
}