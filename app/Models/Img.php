<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Img extends Model
{

    //1景点,2目的地，3路线,4节日，5酒店,6餐厅, 8评价,9建议评价
    const IMG_TYPE_A = 1;
    const IMG_TYPE_B = 4;
    const IMG_TYPE_C = 5;
    const IMG_TYPE_D = 6;
    const IMG_TYPE_E = 8;
    const IMG_TYPE_F = 9;

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

    /**
     * 字段过滤器(当查询获取这个字段时候会触发方法处理字段)
     * @param $value
     * @return string
     */
    public  function getImgUrlAttribute($value)
    {
        if(!substr_count($value, 'http')){
            $value = config('qiniu.qiniuurl') . $value;
        }
        return $value;
    }


    //获取类型下第一张图片
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


    //获取类型下所有图片
    public static function getJoinImgs($joinId,$imgType)
    {

        $data = self::where('img_status','=',self::IMG_STATUS_1)
            ->where('img_type','=',$imgType)
            ->where('join_id','=',$joinId)
            ->orderBy('img_sort')
            ->pluck('img_url')->toArray();
        return $data;
    }



    //批量获取关联的所有图片
    public static function getImgs($joinIds,$imgType)
    {

        $data = self::where('img_status','=',self::IMG_STATUS_1)
            ->where('img_type','=',$imgType)
            ->whereIn('join_id',$joinIds)
            ->pluck('img_url')->toArray();
        return $data;
    }


}
