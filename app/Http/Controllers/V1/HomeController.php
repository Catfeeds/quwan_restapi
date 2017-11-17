<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TokenService;

/**
 * Class HomeController
 * @package App\Http\Controllers\V1
 */
class HomeController extends Controller
{

    protected $tokenService;
    protected $request;
    protected $XS;
    protected $XSIndex;
    protected $XSDocument;
    protected $XSSearch;
    protected $params;

    public function __construct(TokenService $tokenService, Request $request)
    {

        parent::__construct();

        $this->tokenService = $tokenService;
        $this->request = $request;

        //接受到的参数
        $this->params = $this->request->all();

    }

    public function index()
    {
        //获取首页数据

        //return response_success(['data' => $data);
    }

}
