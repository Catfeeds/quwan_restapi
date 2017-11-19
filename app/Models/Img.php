<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Img extends Model
{
    //1景点,2节日，3酒店,4餐厅,5评价
    const IMG_TYPE_1 = 1;
    const IMG_TYPE_2 = 2;
    const IMG_TYPE_3 = 3;
    const IMG_TYPE_4 = 4;
    const IMG_TYPE_5 = 5;

    //0禁用,1启用
    const IMG_STATUS_0 = 0;
    const IMG_STATUS_1 = 1;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'img';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['img_id'];

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
  'img_id' => 'int',
  'join_id' => 'int',
  'img_sort' => 'int',
  'img_type' => 'int',
  'img_status' => 'int',
  'img_created_at' => 'int',
  'img_updated_at' => 'int',
);


    //获取线路下第一张图片
    public static function getOneImg($joinId,$imgType)
    {

        $data = self::select('img_url')
            ->where('img_status','=',self::IMG_STATUS_1)
            ->where('img_type','=',$imgType)
            ->where('join_id','=',$joinId)
            ->orderBy('img_sort')
            ->first();
        return $data->img_url ?? '';
    }


}