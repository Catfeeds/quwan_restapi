<?php

$prefix     = 'quwan';
$namespace  = 'App\Http\Controllers';

//$app->get($prefix, function () {
//    return 'Welcome to quwan';
//});

/**
 * 允许的版本,即当前主版本
 */
$allowVersion = array(1, 2);

$versionAccept = $app->make('request')->header('accept');
$version = getVersion($versionAccept, $allowVersion) ?: '\V1';


//无需用户认证
$unAuthGroup = [
    'prefix'     => $prefix,
    'namespace'  => $namespace.$version,
    'middleware' => ['lang']
];
$app->group($unAuthGroup, function () use ($app) {



    $app->get('/', 'HomeController@wx'); //添加文档到索引

    $app->get('oauth_callback', 'TestController@oauthCallback'); //授权回调
    $app->get('send_hong_bao', 'OrderController@sendHongBao'); //发送红包
    $app->get('add_order', 'OrderController@addOrder'); //创建订单
    $app->get('notify_url', 'OrderController@notifyUrl'); //订单回调
    $app->get('send_moban', 'TestController@sendMoban'); //发送模板消息

    $app->post('add_index', 'TestController@addIndex'); //添加文档到索引
    $app->post('xs', 'TestController@xs'); //迅搜
    $app->get('login', 'TestController@login'); //登录
    $app->post('send_sms', 'TestController@sendSms'); //发送短信
    $app->post('qiniu', 'TestController@qiniu'); //上传到7牛
    $app->post('del_cache', 'TestController@delCache'); //删除指定缓存


    $app->get('add_data', 'HomeController@addData'); //增加默认

    $app->get('home', 'HomeController@index'); //首页数据
    $app->get('mudi/{destination_id}', 'MudiController@index'); //目的地详情页数据
    $app->get('hotel/{hotel_id}', 'HotelController@index'); //酒店详情页数据
    $app->get('hall/{hall_id}', 'HallController@index'); //餐厅详情页数据

});


//需要用户认证
$authGroup = [
    'prefix'     => $prefix,
    'namespace'  => $namespace.$version,
    'middleware' => ['lang', 'jwt']
];
$app->group($authGroup, function () use ($app) {
    $app->get('user', 'TestController@user'); //获取用户user_id
    $app->get('logout', 'TestController@logout'); //登出

});
