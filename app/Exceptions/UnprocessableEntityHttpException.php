<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnprocessableEntityHttpException extends CustomException
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
        $httpStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}