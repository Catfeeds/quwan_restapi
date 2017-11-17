<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'hotel';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['hotel_id'];

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
  'hotel_id' => 'int',
  'hotel_start_at' => 'int',
  'hotel_end_at' => 'int',
  'hotel_sort' => 'int',
  'hotel_status' => 'int',
  'hotel_created_at' => 'int',
  'hotel_updated_at' => 'int',
);

}
