<?php
/**
 * 502 Bad Gateway
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class BadGatewayHttpException extends CustomException
{
    /**
     * BadGatewayHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_BAD_GATEWAY;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}