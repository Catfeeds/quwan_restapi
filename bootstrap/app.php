<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Load a configuration file into the application.
|--------------------------------------------------------------------------
|
| 按需加载自定义配置文件
|
*/

$app->configure('general');
$app->configure('jwt');
$app->configure('dingtalkbot');
$app->configure('dictionary');
$app->configure('qiniu');
$app->configure('sms');
$app->configure('wx');
$app->configure('xs');
;
/*
|--------------------------------------------------------------------------
| Register the facades for the application.
|--------------------------------------------------------------------------
*/

$app->withFacades(false);

/*
|--------------------------------------------------------------------------
| Load the Eloquent library for the application.
|--------------------------------------------------------------------------
*/

$app->withEloquent();


/*
|--------------------------------------------------------------------------
| Define a callback to be used to configure Monolog.
|--------------------------------------------------------------------------
*/
$app->configureMonologUsing(function ($monolog) {

    $path = storage_path('logs/lumen-' . date('Y-m-d') . '.log');

    $handler    = new \Monolog\Handler\StreamHandler($path, \Monolog\Logger::DEBUG);
    $lineFormat = new \Monolog\Formatter\LineFormatter(null, null, true, true);

    $handler->setFormatter($lineFormat);

    $monolog->pushHandler($handler);

    return $monolog;
});


/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);


/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([]);

$app->routeMiddleware([
    'lang' => App\Http\Middleware\ResetLocale::class,
    'jwt' => App\Http\Middleware\Jwt::class,
]);


/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// 系统级 & 第三方 Service Providers
$app->register(Illuminate\Redis\RedisServiceProvider::class);

// 应用级 Service Providers
$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\JwtServiceProvider::class);
$app->register(Vpgame\DingtalkBot\DingtalkBotServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
});

return $app;
