<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    //首页框框状态，1开启，0关闭
    const PAGE_STATUS_0 = 0;
    const PAGE_STATUS_1 = 1;



    //页面类型，1首页广告 2首页热门线路 3首页热门目的地 4首页景点分类 5首页热门景点 6首页节日 7首页推荐周边
    const PAGE_TYPE_1 = 1;
    const PAGE_TYPE_2 = 2;
    const PAGE_TYPE_3 = 3;
    const PAGE_TYPE_4 = 4;
    const PAGE_TYPE_5 = 5;
    const PAGE_TYPE_6 = 6;
    const PAGE_TYPE_7 = 7;


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'home_page';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['home_page_id'];

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'home_page_id' => 'int',
        'home_page_status' => 'int',
        'home_page_type' => 'int',
        'home_page_sort' => 'int',
    ];

    public function getHomeData()
    {
        $data = self::select('*')
            ->where('home_page_status', '=', self::PAGE_STATUS_1)
            ->orderBy('home_page_sort')
            ->get()
            ->toArray();

        return $data;
    }
}
