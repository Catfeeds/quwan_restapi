<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //增加异常邮件事件
        'App\Events\ExceptionNotifyEmailEvent' => [
            'App\Listeners\ExceptionNotifyEmailListener'
        ],
    ];
}
