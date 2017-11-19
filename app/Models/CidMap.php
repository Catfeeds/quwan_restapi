<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CidMap extends Model
{
    //1景点,2目的地，3路线,4节日，5酒店,6餐厅
    const CID_MAP_TYPE_1 = 1;
    const CID_MAP_TYPE_2 = 2;
    const CID_MAP_TYPE_3 = 3;
    const CID_MAP_TYPE_4 = 4;
    const CID_MAP_TYPE_5 = 5;
    const CID_MAP_TYPE_6 = 6;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cid_map';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['cid_map_id'];

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
  'cid_map_id' => 'int',
  'join_id' => 'int',
  'cid_map_sort' => 'int',
  'cid_map_type' => 'int',
);

    /**
     * 获取关联的所有分类id,名称
     * @param $joinId
     * @param $cidMapType
     * @return mixed
     */
    public static function getCidsInfo($joinId, $cidMapType)
    {
        $data = self::select('c.cid_id','c.cid_name')
            ->leftJoin('cid as c', 'c.cid_id', '=', 'cid_map.cid_id')
            ->where('cid_map.join_id','=',$joinId)
            ->where('cid_map.cid_map_type','=',$cidMapType)
            ->orderBy('cid_map.cid_map_sort')
            ->get()->toArray();

        return $data;
    }


    /**
     * 目的地下所有线路分类
     * @param $joinIds
     * @param $cidMapType
     * @return mixed
     */
    public function getMudiLists($joinIds, $cidMapType)
    {
        $data = self::select('c.cid_id','c.cid_name')
            ->leftJoin('cid as c', 'c.cid_id', '=', 'cid_map.cid_id')
            ->whereIn('cid_map.join_id',$joinIds)
            ->where('cid_map.cid_map_type','=',$cidMapType)
            ->orderBy('cid_map.cid_map_sort')
            ->get()->toArray();

        return $data;
    }

}
