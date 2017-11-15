<?php
/**
 * 服务器错误(http 500)异常
 *
 * @package App\Exceptions
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class InternalException extends CustomException
{
    /**
     * InternalException constructor.
     *
     * @param string $error   错误标识
     * @param array  $params  错误信息参数（占位符参数）
     * @param string $prefix  语言包文件名
     */
    public function __construct ($error = '', array $params = [], $prefix = '')
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        parent::__construct($statusCode, $error, $params, $prefix);
    }
}