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



    //$app->get('/', 'HomeController@wx'); //添加文档到索引

//    $app->get('oauth_callback', 'TestController@oauthCallback'); //授权回调
    $app->get('send_hong_bao', 'OrderController@sendHongBao'); //发送红包
    $app->get('add_order', 'OrderController@addOrder'); //创建订单
    $app->get('notify_url', 'OrderController@notifyUrl'); //订单回调
//    $app->get('send_moban', 'TestController@sendMoban'); //发送模板消息

//    $app->post('add_index', 'TestController@addIndex'); //添加文档到索引
//    $app->post('xs', 'TestController@xs'); //迅搜
    $app->post('login', 'LoginController@login'); //登录

//    $app->post('send_sms', 'TestController@sendSms'); //发送短信
    //$app->post('qiniu', 'TestController@qiniu'); //上传到7牛
//    $app->post('del_cache', 'TestController@delCache'); //删除指定缓存


//    $app->get('add_data', 'HomeController@addData'); //增加默认

    $app->get('home', 'HomeController@index'); //首页数据
    $app->get('mudi/{destination_id}', 'MudiController@index'); //目的地详情页数据
    $app->get('hotel/{hotel_id}', 'HotelController@index'); //酒店详情页数据
    $app->get('hall/{hall_id}', 'HallController@index'); //餐厅详情页数据
    $app->get('holiday/{holiday_id}', 'HolidayController@index'); //节日详情页数据
    $app->get('attractions/{attractions_id}', 'AttractionsController@index'); //景点详情页数据
    $app->get('route/{route_id}', 'RouteController@index'); //线路详情页数据




});


//需要用户认证
$authGroup = [
    'prefix'     => $prefix,
    'namespace'  => $namespace.$version,
    'middleware' => ['lang', 'jwt']
];
$app->group($authGroup, function () use ($app) {

    $app->post('use_route', 'RouteController@use'); //使用线路
    $app->post('fav', 'FavController@index'); //收藏/取消
    $app->get('score', 'ScoreController@index'); //评价列表
    $app->post('add_score', 'ScoreController@add'); //发布评价
    $app->post('add_suggest', 'SuggestController@addSuggest'); //发布建议反馈


    $app->post('edit_lbs', 'UserController@editLbs'); //修改用户经纬度信息
    $app->get('user_info', 'UserController@userInfo'); //获取用户信息
    $app->post('edit_user_info', 'UserController@editUserInfo'); //编辑用户信息
    $app->post('bind_mobile', 'UserController@bindMobile'); //绑定用户手机

    $app->get('order_count', 'UserController@orderCount'); //订单统计信息
    $app->get('message_list', 'MessageController@messageList'); //消息列表
    $app->get('message_info/{message_id}', 'MessageController@messageInfo'); //消息详情
    $app->post('message_read', 'MessageController@messageRead'); //所有消息已读



    $app->post('send_sms', 'UserController@sendSms'); //发送短信
    $app->post('qiniu', 'UserController@qiniu'); //上传到7牛

    $app->get('logout', 'UserController@logout'); //登出


});
