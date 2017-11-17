<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attractions extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'attractions';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['attractions_id'];

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
  'attractions_id' => 'int',
  'shop_id' => 'int',
  'attractions_is_refund' => 'int',
  'attractions_start_at' => 'int',
  'attractions_end_at' => 'int',
  'attractions_sort' => 'int',
  'attractions_status' => 'int',
  'attractions_created_at' => 'int',
  'attractions_updated_at' => 'int',
);

}
