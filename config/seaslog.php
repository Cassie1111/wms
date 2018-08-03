<?php

return [
    // 启用seaslog日志
    'enable' => true,

    // log根目录
    'base_path' => '/var/log/inner_web',

    // logger目录
    'logger' => env('APP_NAME', 'default'),
];
