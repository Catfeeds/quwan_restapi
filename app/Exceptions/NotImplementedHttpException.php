<?php
/**
 * 501 Not Implemented
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class NotImplementedHttpException extends CustomException
{
    /**
     * NotImplementedHttpException constructor.
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