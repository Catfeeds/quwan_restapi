<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'holiday';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['holiday_id'];

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
  'holiday_id' => 'int',
  'shop_id' => 'int',
  'holiday_start_at' => 'int',
  'holiday_end_at' => 'int',
  'holiday_sort' => 'int',
  'holiday_status' => 'int',
  'holiday_created_at' => 'int',
  'holiday_updated_at' => 'int',
);

}
