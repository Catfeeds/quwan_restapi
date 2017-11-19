<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fav extends Model
{
    //'1景点,2节日，3酒店,4餐厅',
    const FAV_TYPE_1 = 1;
    const FAV_TYPE_2 = 2;
    const FAV_TYPE_3 = 3;
    const FAV_TYPE_4 = 4;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'fav';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['fav_id'];

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
        'fav_id' => 'int',
        'join_id' => 'int',
        'fav_type' => 'int',
        'user_id' => 'int',
        'fav_created_at' => 'int',
        'fav_updated_at' => 'int',
    );


    /**
     * 检测是否已收藏
     * @param $favType
     * @param $userId
     * @param $joinId
     * @return mixed
     */
    public function isFav($favType, $userId, $joinId)
    {
        $favId = self::where('fav_type', '=', $favType)
            ->where('user_id', '=', $userId)
            ->where('join_id', '=', $joinId)
            ->value('fav_id');
        return $favId;
    }

    /**
     * 添加收藏
     * @param $arr
     * @return static
     */
    public function add($arr) {
        $favId = self::create($arr);
        return $favId;
    }

}
