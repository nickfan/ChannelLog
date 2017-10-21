<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2017/4/14
 * Time: 15:33
 */

namespace Nickfan\ChannelLog;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

class ChannelLogDefaultConfiguratorStandAlone implements ChannelLogConfigurator
{

    public function configure(Logger $logger, $channel, $settings=[])
    {
        $settings+=[
            'path'=>'',
            'level' => Logger::DEBUG,
            'log'=>'single',
            'log_syslog_name'=>'channel_log',
            'log_max_files'=>5,
        ];
        $path = $settings['path'];
        $log = $settings['log'];
        if(empty($path)){
            throw new \InvalidArgumentException('log file path required');
        }
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if(strpos($path,':'.DIRECTORY_SEPARATOR)===false){
                throw new \InvalidArgumentException('relative path not allowed');
            }
        }else{
            if($path[0] !== DIRECTORY_SEPARATOR){
                throw new \InvalidArgumentException('relative path not allowed');
            }
        }
        $level = $settings['level'];

        switch ($log){
            case 'daily':
                $days = $settings['log_max_files'];
                $formatter = new LineFormatter(null, null, false, true);
                $logger->pushHandler(
                    $handler = new RotatingFileHandler($path, $days, $level)
                );
                $handler->setFormatter($formatter);
                break;
            case 'syslog':
                $name = $settings['log_syslog_name'];
                $logger->pushHandler(new SyslogHandler($name, LOG_USER, $level));
                break;
            case 'errorlog':
                $messageType = ErrorLogHandler::OPERATING_SYSTEM;
                $formatter = new LineFormatter(null, null, false, true);
                $logger->pushHandler(
                    $handler = new ErrorLogHandler($messageType, $level)
                );
                $handler->setFormatter($formatter);
                break;
            case 'single':
            default:
                $logger->pushHandler(
                    new ChannelLogStreamHandler(
                        $channel,
                        $path,
                        $level
                    )
                );
                break;
        }

        return $logger;
    }
}