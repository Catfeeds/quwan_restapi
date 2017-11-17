<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CidMap extends Model
{

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

}
