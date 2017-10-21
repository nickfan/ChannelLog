<?php

return [
    'default' => [
        /*
        |--------------------------------------------------------------------------
        | Logging Configuration
        |--------------------------------------------------------------------------
        | Available Settings: "single", "daily", "syslog", "errorlog"
        |
        */
        'log' => 'single',
        'path' => 'logs/default.log',
        'level' => \Monolog\Logger::DEBUG
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
