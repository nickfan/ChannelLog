# ChannelLog

## 简介

支持多通道（多实例）配置的Laravel 日志组件（基于Monolog）

此项目基于 [StackOverflow上的一条自定义日志的问答](http://stackoverflow.com/questions/37809989/laravel-5-2-custom-log-file-for-different-tasks)

## 安装

* composer 安装组件

```
composer require "nickfan/channel-log:dev-master"
```

* 修改config/app.php配置：

 **providers 组中增加：**
 ```
Nickfan\ChannelLog\ChannelLogServiceProvider::class,
 ```

 **aliases 组中增加：**
```
'ChannelLog' => Nickfan\ChannelLog\Facades\ChannelLog::class,
```

* 配置文件发布

```
php artisan vendor:publish --provider="Nickfan\ChannelLog\ChannelLogServiceProvider"
```

## 配置

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
    'custom'=>[  // 频道标识key
        'name'=>'custom',           // (可选) 频道标识key
        'log'=>'daily',             // 日志模式 支持 console 只在终端输出; single 单文件模式; daily 每日文件模式; syslog 系统日志模式; errorlog php系统配置中的errorlog;
        'console'=>true,            // 在记录到文件流的同时是否同步输出到终端
        'path'=>'logs/custom.log', // 日志文件路径 相对路径会存储在storage_path中
        'level' => \Monolog\Logger::DEBUG, // 日志等级
        'log_syslog_name'=>'channel_log',   // 当日志模式为 syslog时 系统日志中的项目名称
        'log_max_files'=>5,                 // 当日志模式为 daily时，最大保留的日志文件个数(天数)
    ],
];

```


## 自定义配置器

单频道配置信息中可以根据**configurator**设定自定义的Logger配置器，

配置器必须实现 ```Nickfan\ChannelLog\ChannelLogConfigurator ``` 接口

具体可以参考 ```Nickfan\ChannelLog\ChannelLogDefaultConfigurator``` 的默认配置器

例如：

```
return [
    'myconsole' => [  // 频道标识key
        'path' => 'logs/default.log', // 日志文件路径 相对路径会存储在storage_path中
        'level' => \Monolog\Logger::DEBUG // 日志等级

        // 设定配置器使用的类名
        'configurator' => \App\Support\ChannelLogMyConsoleConfigurator::class
    ],

];
```


## laravel下用法

```

// 往 default 频道写入一条info级别的日志
ChannelLog::channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);

// 往 event 频道写入一条error级别的日志
ChannelLog::channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

// 以daily日志模式往/tmp/mycustom-2017-10-24.log的日志文件中写入一条debug级别的日志
ChannelLog::daily('/tmp/mycustom.log')->debug('my custom message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

// 以single日志模式往/tmp/newdirect.log的日志文件中写入一条debug级别的日志同时输出到终端
ChannelLog::direct([
            'name'=>'newdirect',// 频道标识key
            'console'=>true,    // 同步输出到终端
            'path'=>'/tmp/newdirect.log', // 日志文件实际路径
            ])->debug('new direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

```

## 单独使用

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


**其他可以参考Laravel自带的日志的用法或者MonoLog官方 https://github.com/Seldaek/monolog**


