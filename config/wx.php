<?php
return array(
//    'debug' => true,
//    'app_id' => 'wxfee3ef526ee96c44',
//    'secret' => '3ad40eb4cdd69b78161ad6338c378a9e',
//    'token' => 'quwan',
    'debug' => true,
    'app_id' => 'wxb74f749dec2016f6',
    'secret' => '300b037d3ba4252d73bf7c0a36317b2c',
    'token' => 'quwan',



    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file' => base_path() . '/storage/logs/wx.log', // XXX: 绝对路径！！！！
    ],
    'oauth' => [
        'scopes' => ['snsapi_userinfo'], // snsapi_userinfo详情信息
        'callback' => '/quwan/oauth_callback',
    ],
    'payment' => [
        'merchant_id'        => '1454303702',
        'key'                => 'WM4gTS60zN3KqORQGel7lCsDdCotVbGJ',
        'cert_path'          => base_path() . '/config/apiclient_cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => base_path() . '/config/apiclient_key.pem',      // XXX: 绝对路径！！！！
        'notify_url'         => '/quwan/notify_url',       // 你也可以在下单时单独设置来想覆盖它
    ],
);
