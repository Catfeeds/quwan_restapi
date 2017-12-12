<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedStatus extends Model
{
    //1开启，0关闭
    const RED_STATUS_0 = 0;
    const RED_STATUS_1 = 1;



    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'red_status';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['red_id'];

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
    protected $casts = [
        'red_id' => 'int',
        'red_status' => 'int',
        'red_start_num' => 'int',
        'red_end_num' => 'int',
    ];

    //获取红包设置
    public static function getSet()
    {
        $res = self::where('red_id','=',1)->first();
        if(!$res){
            return [
                'red_status' => 0,
                'red_start_num' => 0,
                'red_end_num' => 0,
            ];
        }
        $res = $res->toArray();
        unset($res['red_id']);
        return $res;
    }
}
