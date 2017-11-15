<?php
/**
 * 网络请求错误(http 400)异常
 *
 * @author      张光强 <zhangguangqiang@vpgame.cn>
 * @version     v0.1 2016/12/12 15:37:46
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * 网络请求错误
 *
 * @author 张光强 <zhangguangqiang@vpgame.cn>
 */
class BadRequestHttpException extends CustomException
{
    /**
     * BadRequestHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_BAD_REQUEST;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}