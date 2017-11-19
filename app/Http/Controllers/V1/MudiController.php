<?php

namespace App\Http\Controllers\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Destination;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class HomeController
 * @package App\Http\Controllers\V1
 */
class MudiController extends Controller
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

    public function index($destination_id = 0)
    {
        $destination_id = $destination_id ?? 0;

        //获取目的地详情
        $data = Destination::where('destination_id','=',$destination_id)->first();
        if (!$data) {
            throw new UnprocessableEntityHttpException(850004);
        }
        $data = $data->toArray();

        //所有景点图片
        $attractions = Attractions::select()->where('destination_id','=',$destination_id)->orderBy('attractions_sales_num DESC')->get()->toArray();

        //所有线路分类

        //2个销量最好的景点

        //个使用最多的线路

        //两个评价最多的酒店

        //两个评价最多的餐厅
        var_dump($destination_id,$data);
    }

}
