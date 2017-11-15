<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class TooManyRequestsHttpException extends CustomException
{
    /**
     * ServiceUnavailableHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_TOO_MANY_REQUESTS;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}