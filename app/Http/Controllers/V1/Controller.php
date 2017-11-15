<?php

namespace App\Http\Controllers\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;

/**
 * Class Controller
 *
 * 基类Controller
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    const ERR = 100;

    public $userId;
    public $client;
    public $authorization;
    public $clientIp;
    public $errorCodePrefix;

    public function __construct()
    {
        $this->userId = (int) Auth::id();
        $this->client = Request::get('vp-request-client');
        $this->authorization = Request::header('authorization') ?? null;
        $this->clientIp =Request::header('user-client-ip') ?? null;
        $this->errorCodePrefix = Config::get('general')['error_code_prefix'];
        Request::offsetUnset('vp-request-client');
    }

}
