<?php
return array(
    'debug' => true,
    'app_id' => 'wxfee3ef526ee96c44',
    'secret' => '3ad40eb4cdd69b78161ad6338c378a9e',
    'token' => 'quwan',

    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file' => base_path() . '/storage/logs/wx.log', // XXX: 绝对路径！！！！
    ],
    'oauth' => [
        'scopes' => ['snsapi_base'],
        'callback' => '/quwan/oauth_callback',
    ],
);
