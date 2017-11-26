<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggest extends Model
{
    //0未回复，1已经回复
    const SUGGEST_REPLAY_STATUS_0 = 0;
    const SUGGEST_REPLAY_STATUS_1 = 1;


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'suggest';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['suggest_id'];

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
        'suggest_id' => 'int',
        'user_id' => 'int',
        'suggest_replay_status' => 'int',
        'suggest_created_at' => 'int',
        'suggest_updated_at' => 'int',


    );

}
