<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    //0禁用,1启用
    const DESTINATION_STATUS_0 = 0;
    const DESTINATION_STATUS_1 = 1;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'destination';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['destination_id'];

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
    protected $casts = array(
        'destination_id' => 'int',
        'destination_status' => 'int',
        'destination_created_at' => 'int',
        'destination_updated_at' => 'int',
    );

    public function getInfo($destinationId = 0)
    {
        //获取目的地详情
        $data = self::select('destination_id', 'destination_name')
            ->where('destination_id', '=', $destinationId)
            ->where('destination_status', '=', self::DESTINATION_STATUS_1)
            ->first();

        return $data;
    }
}
