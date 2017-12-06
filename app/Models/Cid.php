<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cid extends Model
{

    //分类类型(1景点,2目的地，3路线,4节日，5酒店,6餐厅)
    const CID_TYPE_A = 1;
    const CID_TYPE_B = 2;
    const CID_TYPE_C = 3;
    const CID_TYPE_D = 4;
    const CID_TYPE_E = 5;
    const CID_TYPE_F = 6;

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


    /**
     * 获取所有分类信息
     * @return mixed
     */
    public static function getAll()
    {

        $data = self::where('cid_status','=',self::CID_STATUS_1)
            ->pluck('cid_name','cid_id')->toArray();

        return $data;
    }

}
