<?php
return array(
    'upload_size' => 5120000,
    'bucketname' => env('QINIU_BUCKETNAME', ''),
    'accesskey' => env('QINIU_ACCESSKEY', ''),
    'secretkey' => env('QINIU_SECRETKEY', ''),
    'qiniuurl' => env('QINIU_QINIUURL', ''),
    'extensions' =>  ['image/jpeg', 'image/png', 'image/gif','application/octet-stream','image/jpg'],
);
