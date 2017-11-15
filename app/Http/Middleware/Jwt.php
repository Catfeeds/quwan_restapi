<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedHttpException;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Jwt
{
    const ERR = 850;

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     *
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            throw new UnauthorizedHttpException(ecode(self::ERR, 0));
        }

        return $next($request);
    }
}
