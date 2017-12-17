<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // 0女,1男,2保密
    const USER_SEX_0 = 0;
    const USER_SEX_1 = 1;
    const USER_SEX_2 = 2;


    //'手机绑定(0未绑定,1已绑定)',
    const USER_IS_BINDING_0 = 0;
    const USER_IS_BINDING_1 = 1;

    // '0禁用,1启用',
    const USER_STATUS_0 = 0;
    const USER_STATUS_1 = 1;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected $guarded = ['user_id'];

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
        'user_id' => 'int',
        'user_sex' => 'int',
        'user_is_binding' => 'int',
        'user_msg_num' => 'int',
        'user_status' => 'int',
        'user_created_at' => 'int',
        'user_updated_at' => 'int',
    );


    /**
     * 字段过滤器(当查询获取这个字段时候会触发方法处理字段)
     * @param $value
     * @return string
     */
    public  function getUserSexAttribute($value)
    {
        $value = (int)$value === self::USER_SEX_0 ? '女' : '男';
        return $value;
    }

    /**
     * 获取用户详情
     * @param $userId
     * @return array
     */
    public function getInfo($userId)
    {
        $data = self::select('user_id', 'user_nickname', 'user_sex', 'user_avatar', 'user_mobile', 'user_is_binding',
            'openid', 'user_lon', 'user_lat', 'user_geohash', 'user_msg_num', 'user_status', 'user_created_at')
            ->where('user_id', '=', $userId)->first();
        if (!$data) {
            return [];
        }
        $data = $data->toArray();
        return $data;
    }

    //添加用户
    public function register($params)
    {
        //检测用户是否存在不存在注册
        $userId = User::where('openid','=', $params['openid'])->value('user_id');
        if (!$userId) {
            $arr = [
                'user_nickname' => $params['user_nickname'],
                'user_sex'  => $params['user_sex'],
                'user_avatar'  => $params['user_avatar'],
                'openid'  => $params['openid'],
                'user_created_at' => time(),
                'user_updated_at'  => time(),
            ];
            $userId = User::create($arr);
        }
        
        return $userId;
    }

    //修改用户经纬度
    public function editLbs($userId,$params)
    {
        $arr = [
            'user_lon'=>$params['user_lon'],
            'user_lat'=>$params['user_lat'],
            'user_updated_at'=>time(),
        ];
        $tag = User::where('user_id','=', $userId)->update($arr);

        return $tag;
    }

    /**
     * 获取用户经纬度
     * @param $userId
     * @return array
     */
    public static function getUserLon($userId)
    {
        $data = self::select('user_lon', 'user_lat')
            ->where('user_id', '=', $userId)->first();
        if (!$data) {
            return [];
        }
        $data = $data->toArray();
        return $data;
    }

    //修改用户信息
    public function editInfo($userId,$params)
    {
        $arr = [
            'user_nickname'=>$params['user_nickname'],
            'user_sex'=>$params['user_sex'],
            'user_avatar'=>$params['user_avatar'],
            'user_mobile'=>$params['user_mobile'],
            'user_updated_at'=>time(),
        ];
        $tag = User::where('user_id','=', $userId)->update($arr);

        return $tag;
    }

    //绑定用户手机
    public function bindMobile($userId,$userMobile)
    {
        $arr = [
            'user_is_binding'=>self::USER_IS_BINDING_1,
            'user_mobile'=>$userMobile,
            'user_updated_at'=>time(),
        ];
        $tag = User::where('user_id','=', $userId)->update($arr);

        return $tag;
    }

}
