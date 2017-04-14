# ChannelLog

[README in Chinese | 中文说明](README.zh-CN.md)

## Intro
Support Laravel Log data to separate channel log files with config.


this project base on
[ShQ's answer for laravel 5.2 custom log file for different tasks](http://stackoverflow.com/questions/37809989/laravel-5-2-custom-log-file-for-different-tasks)


## Install

* install composer package

```
composer require "nickfan/channel-log:dev-master"
```

* Edit config/app.php file

 **providers group add:**
 ```
Nickfan\ChannelLog\ChannelLogServiceProvider::class,
 ```

 **aliases group add:**
```
'ChannelLog' => Nickfan\ChannelLog\Facades\ChannelLog::class,
```

* publish config file:

```
php artisan vendor:publish --provider="Nickfan\ChannelLog\ChannelLogServiceProvider"
```

## Setup

```
return [
    'default' => [  // Channel Label Name
        'path' => 'logs/default.log', // Log file path,relative path will convert to relative with storage_path(yourpath)
        'level' => \Monolog\Logger::DEBUG // Log Level
    ],
//    'event' => [
//        'path' => 'logs/event.log',
//        'level' => \Monolog\Logger::INFO
//    ],

];

```


## Custom Configurator

in a channel settings your could set your custom configurator by set
a key name: ```configurator```

the Configurator Class Must implements  ```Nickfan\ChannelLog\ChannelLogConfigurator ``` interface

you could write your own configurator reference
```Nickfan\ChannelLog\ChannelLogDefaultConfigurator``` the default configurator class

config example:

```
return [
    'myconsole' => [  // Channel Label Name
        'path' => 'logs/default.log', // Log file path,relative path will convert to relative with storage_path(yourpath)
        'level' => \Monolog\Logger::DEBUG // Log Level
        // set the configurator class name
        'configurator' => \App\Support\ChannelLogMyConsoleConfigurator::class
    ],

];
```


## Usage

```

// Log to 'default' channel
ChannelLog::channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);

// Log to 'event' channel
ChannelLog::channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

```


**more detail usage reference monolog offical site**

