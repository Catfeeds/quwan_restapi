<?php

namespace App\Exceptions;

use App\Events\ExceptionNotifyEmailEvent;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    const ANY_ERROR = 999999;
    
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * 通过文件系统记录异常信息
     *
     * @param Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        $serverIP = 'Server IP '.($_SERVER['SERVER_ADDR'] ?? 'Unknown IP');

        if ($e instanceof CustomException) {
            $statusCode = $e->getStatusCode();

            $error = [
                $serverIP,
                get_class($e),
                $statusCode,
                [$e->getCode() => $e->getMessage()],
                $e->getFile() . ':' . $e->getLine(),
            ];

            Log::error('CustomException:', $error);
        } elseif ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();

            $error = [
                $serverIP,
                get_class($e),
                $statusCode,
                $e->getHeaders(),
                $e->getFile() . ':' . $e->getLine(),
            ];

            Log::error('HttpException:', $error);
        } elseif ($e instanceof ModelNotFoundException) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            $error = [
                $serverIP,
                get_class($e),
                $statusCode,
                $e->getMessage(),
                $e->getFile() . ':' . $e->getLine(),
            ];

            Log::error('ModelException:', $error);
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = [
                $serverIP,
                get_class($e),
                $statusCode,
                $e->getMessage(),
                $e->getFile() . ':' . $e->getLine(),
                $e->getTraceAsString()
            ];
    
            Log::error('Exception:', $error);

            parent::report($e);
        }

        // 生产环境下，异常日志监听事件
        if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR &&
            App::environment('production')) {
            event(new ExceptionNotifyEmailEvent($e));
        }

    }

    /**
     * 异常信息抛出 (面向客户端)
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof CustomException) {
            return response_error(
                $e->getCode(),
                $e->getMessage(),
                $e->getStatusCode(),
                $e->getExtraInfo()
            );
        }

        $isDebug = env('APP_DEBUG', true);
        if (!$isDebug) {
            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
            } else {
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            return response_error(self::ANY_ERROR, 'System Error.', $statusCode);
        }

        return parent::render($request, $e);
    }
}
