<?php

namespace App\Listeners;

use App\Events\ExceptionNotifyEmailEvent;
use Vpgame\DingtalkBot\Facades\DingtalkBot;

class ExceptionNotifyEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ExceptionNotifyEmailEvent  $event
     * @return void
     */
    public function handle(ExceptionNotifyEmailEvent $event)
    {
        //使用钉钉发送告警
        DingtalkBot::send($event->exception);
    }
}
