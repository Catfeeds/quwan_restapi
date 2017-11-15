<?php

namespace Vpgame\DingtalkBot\Facades;

use Illuminate\Support\Facades\Facade;

class DingtalkBot extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dingtalkbot';
    }

}