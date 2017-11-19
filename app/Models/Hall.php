<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    //'0禁用,1启用',
    const HALL_STATUS_0 = 0;
    const HALL_STATUS_1 = 1;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'hall';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['hall_id'];

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
        'hall_id' => 'int',
        'hall_score' => 'int',
        'hall_evaluation' => 'int',
        'hall_start_at' => 'int',
        'hall_end_at' => 'int',
        'hall_sort' => 'int',
        'hall_status' => 'int',
        'hall_created_at' => 'int',
        'hall_updated_at' => 'int',
    );

    /**
     * 目的->评价最好的餐厅
     * @param $hallIds
     * @return array
     */
    public function getMudiList($hallIds)
    {

        $data = self::select('hall_id', 'hall_name', 'hall_score', 'hall_evaluation')
            ->where('hall_sort', '=', self::HALL_STATUS_1)
            ->whereIn('hall_id', $hallIds)
            ->orderBy('hall_score_num', 'desc')
            ->limit(2)
            ->get()
            ->toArray();
        if (true === empty($data)) {
            return [];
        }

        foreach ($data as $keyR => &$valueR) {
            //图片
            $valueR['img'] = RouteDayJoin::getOneJoinImg($valueR['hall_id']);

        }

        return $data;
    }
}
