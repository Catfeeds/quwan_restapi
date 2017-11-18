<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cid extends Model
{

    //分类类型(1景点,2目的地，3路线,4节日，5酒店,6餐厅)
    const CID_TYPE_1 = 1;
    const CID_TYPE_2 = 2;
    const CID_TYPE_3 = 3;
    const CID_TYPE_4 = 4;
    const CID_TYPE_5 = 5;
    const CID_TYPE_6 = 6;

    //0禁用,1启用
    const CID_STATUS_0 = 0;
    const CID_STATUS_1 = 1;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cid';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['cid_id'];

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
    protected $casts = array (
  'cid_id' => 'int',
  'cid_type' => 'int',
  'cid_sort' => 'int',
  'cid_status' => 'int',
);




}
