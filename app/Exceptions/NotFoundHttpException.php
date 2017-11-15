<?php
/**
 * 404异常
 */

namespace App\Exceptions;

use Illuminate\Http\Response;

class NotFoundHttpException extends CustomException
{
    /**
     * NotFoundHttpException constructor.
     * @param string $code
     * @param array $extra
     * @param string $prefix
     * @param array $params
     */
    public function __construct ($code, $extra = [], $prefix = '', array $params = [])
    {
        $httpStatusCode = Response::HTTP_NOT_FOUND;

        parent::__construct($httpStatusCode, $code, $extra, $prefix, $params);
    }
}