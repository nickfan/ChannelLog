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
    'default' => [  // 频道标识key
        'path' => 'logs/default.log', // 日志文件路径 相对路径会存储在storage_path中
        'level' => \Monolog\Logger::DEBUG // 日志等级
    ],
//    'event' => [
//        'path' => 'logs/event.log',
//        'level' => \Monolog\Logger::INFO
//    ],

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


## 用法

```

// 往 default 频道写入一条info级别的日志
ChannelLog::channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);

// 往 event 频道写入一条error级别的日志
ChannelLog::channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

```


**其他可以参考Laravel自带的日志的用法或者MonoLog官方**
