<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Config;
use RuntimeException;
use Illuminate\Support\Facades\Lang;

abstract class CustomException extends RuntimeException
{
    /**
     * http 状态码 (HTTP response status codes)
     *
     * @var
     *
     * @link https://tools.ietf.org/html/rfc7231
     */
    protected $httpStatusCode;

    /**
     * 异常时额外信息
     * @var
     */
    protected $extra;

    /**
     * CustomException constructor.
     *
     * @param string $httpStatusCode    HTTP 状态码
     * @param int $code                 自定义错误码
     * @param array $extra                自定义额外信息
     * @param array   $params           错误信息参数（占位符参数）
     * @param string  $prefix           语言包文件名
     */
    public function __construct($httpStatusCode, $code, $extra, $prefix = '', array $params = [])
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->code = $code;
        $this->extra = $extra;

        $prefix = empty($prefix)
            ? 'error'
            : strtolower($prefix);

        $message = Lang::get("{$prefix}.{$this->code}", $params);

        $this->code = Config::get('general')['error_code_prefix'].$this->code;

        parent::__construct($message);
    }

    /**
     * 获取状态码
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * 获取额外信息
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return $this->extra;
    }
}
