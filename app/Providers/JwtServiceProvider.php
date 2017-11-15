<?php

namespace App\Providers;

use App\Services\TokenService;
use Illuminate\Support\ServiceProvider;
use App\Services\Auth\JwtGuard;

class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->extend('jwt', function ($app) {
            $bearerToken = $app['request']->server->getHeaders()['AUTHORIZATION'] ?? '';
            $tokenService = new TokenService();
            $guard = new JwtGuard($bearerToken, $tokenService);
            return $guard;
        });
    }
}
