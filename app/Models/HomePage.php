<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    //首页框框状态，1开启，0关闭
    const PAGE_STATUS_0 = 0;
    const PAGE_STATUS_1 = 1;

    //页面类型，1广告，2路线，3目的地，4景点，5节日，6推荐周边
    const PAGE_TYPE_1 = 1;
    const PAGE_TYPE_2 = 2;
    const PAGE_TYPE_3 = 3;
    const PAGE_TYPE_4 = 4;
    const PAGE_TYPE_5 = 5;
    const PAGE_TYPE_6 = 6;


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

    public function getHomeData($value = 0)
    {
//        CREATE TABLE `qw_home_page` (
//    `home_page_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '页面id',
//  `home_page_name` varchar(50) NOT NULL COMMENT '页面名称',
//  `home_page_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页框框状态，1开启，0关闭',
//  `home_page_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '页面类型，1广告，2路线，3目的地，4景点，5节日，6推荐周边',
//  `home_page_sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序，小在前',
//  PRIMARY KEY (`home_page_id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页控制开关';


        $data = self::select('home_page.*', 'v.value_id', 'v.sort')
            ->leftJoin('home_page_value as v', 'v.home_page_id', '=', 'home_page.home_page_id')
            ->where('home_page.home_page_id', '=', 1)
            ->where('home_page.home_page_status', '=', self::PAGE_STATUS_1)
            ->orderBy('home_page.home_page_sort, v.sort')
            ->toArray();

        return $data;
    }
}
