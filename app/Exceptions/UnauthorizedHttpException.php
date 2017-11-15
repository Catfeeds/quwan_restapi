<?php
/**
 * 401异常 Unauthorized
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedHttpException extends CustomException
{
    /**
     * UnauthorizedHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_UNAUTHORIZED;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}