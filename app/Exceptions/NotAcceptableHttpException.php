<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class NotAcceptableHttpException extends CustomException
{
    /**
     * NotAcceptableHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_NOT_ACCEPTABLE;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}