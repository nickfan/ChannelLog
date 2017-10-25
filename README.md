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
    'default' => [
        'path' => 'logs/default.log',
        'level' => \Monolog\Logger::DEBUG
    ],
//    'event' => [
//        'path' => 'logs/event.log',
//        'level' => \Monolog\Logger::INFO
//    ],
    'custom'=>[  // Channel Label Name
        'name'=>'custom',           // (optional) Channel Label Name
        'log'=>'daily',             // log mode support: console (only in stdout); single (single file); daily (daily files); syslog (syslog); errorlog (php errorlog);
        'console'=>true,            // output to stdout(console) at the sametime.
        'path'=>'logs/custom.log', // Log file path,relative path will convert to relative with storage_path(yourpath)
        'level' => \Monolog\Logger::DEBUG, // Log Level
        'log_syslog_name'=>'channel_log',   // syslog mode log entry name
        'log_max_files'=>5,                 // max files keep in daily log mode
    ],
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


## Laravel Usage

```

// Log to 'default' channel
ChannelLog::channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);

// Log to 'event' channel
ChannelLog::channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

// Log to 'mycustom' channel in daily log mode ,with filepath '/tmp/mycustom-2017-10-24.log'
ChannelLog::daily('/tmp/mycustom.log')->debug('my custom message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

// Log to 'newdirect' channel in single log mode ,with filepath '/tmp/newdirect.log' , also log to console(stdout)
ChannelLog::direct([
            'name'=>'newdirect',// Channel Label Name
            'console'=>true,    // output to console(stdout)
            'path'=>'/tmp/newdirect.log', // log filepath
            ])->debug('new direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);


```

## Standalone Usage

```
use Nickfan\ChannelLog\ChannelLogWriterStandAlone;

$projectRoot = dirname(__DIR__);
$channelLogWriter = new ChannelLogWriterStandAlone(
  [
      'default'=>[
          'log' => 'single',
          'console'=> false,
          'path'=>$projectRoot.'/logs/default.log',
          'level'=>\Monolog\Logger::INFO,
      ],
      'event' => [
          'log' => 'daily',
          'console'=> true,
          'path' => $projectRoot.'/logs/event.log',
          'level' => \Monolog\Logger::DEBUG,
      ],
  ]
);

$channelLogWriter->channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);


$result2 = $channelLogWriter->channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);


$result3 = $channelLogWriter->daily($projectRoot.'/logs/mycustom.log')->debug('my custom message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);


$result4 = $channelLogWriter->direct($projectRoot.'/logs/mydirect.log')->debug('my direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);


$result5 = $channelLogWriter->direct([
            'name'=>'newdirect',
            'console'=>true,
            'path'=>$projectRoot.'/logs/newdirect.log',
            ])->debug('new direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

```


**more detail usage reference monolog offical site https://github.com/Seldaek/monolog**

