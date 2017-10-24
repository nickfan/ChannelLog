<?php

return [
    'default' => [
        /*
        |--------------------------------------------------------------------------
        | Logging Configuration
        |--------------------------------------------------------------------------
        | Available Settings: "single", "daily", "syslog", "errorlog", "console"
        |
        */
        'log'=>'single',
        'console'=>false,
        'path' => 'logs/default.log',
        'level' => \Monolog\Logger::DEBUG,
    ],
    'console'=>[
        'log'=>'console',
        'console'=>false,
        'path'=>'php://stdout',
        'level' => \Monolog\Logger::DEBUG,
    ],
    'custom'=>[
        'name'=>'custom',
        'log'=>'daily',
        'console'=>true,
        'path'=>'logs/custom.log',
        'level' => \Monolog\Logger::DEBUG,
        'log_syslog_name'=>'channel_log',
        'log_max_files'=>5,
    ],
//    'event' => [
//        'log' => 'daily',
//        'path' => 'logs/event.log',
//        'level' => \Monolog\Logger::INFO
//    ],
//    'audit' => [
//        'log' => 'daily',
//        'path' => 'logs/audit.log',
//        'level' => \Monolog\Logger::INFO
//    ],
//    'queue' => [
//        'log' => 'daily',
//        'path' => 'logs/queue.log',
//        'level' => \Monolog\Logger::INFO
//    ],
//    'ui' => [
//        'log' => 'daily',
//        'path' => 'logs/queue.log',
//        'level' => \Monolog\Logger::INFO
//    ],
//    'api' => [
//        'log' => 'syslog',
//        'path' => 'logs/api.log',
//        'level' => \Monolog\Logger::INFO
//    ],
];
