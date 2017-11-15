<?php
/**
 * 服务器错误(http 500)异常
 *
 * @package App\Exceptions
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class InternalServerErrorException extends CustomException
{
    /**
     * InternalException constructor.
     * @param string $code      错误码
     * @param array $extra
     * @param string $prefix    语言包文件名
     * @param array $params     错误信息参数（占位符参数）
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}