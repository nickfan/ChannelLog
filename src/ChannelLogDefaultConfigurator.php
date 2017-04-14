<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2017/4/14
 * Time: 15:33
 */

namespace Nickfan\ChannelLog;


use Monolog\Logger;

class ChannelLogDefaultConfigurator implements ChannelLogConfigurator
{

    public function configure(Logger $logger, $channel, $settings=[])
    {
        $settings+=[
            'path'=>'',
            'level' => Logger::DEBUG
        ];
        $path = $settings['path'];
        if(empty($path)){
            throw new \InvalidArgumentException('log file path required');
        }
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if(strpos($path,':'.DIRECTORY_SEPARATOR)===false){
//                throw new \InvalidArgumentException('relative path not allowed');
                $path = storage_path($path);
            }
        }else{
            if($path[0] !== DIRECTORY_SEPARATOR){
//                throw new \InvalidArgumentException('relative path not allowed');
                $path = storage_path($path);
            }
        }
        $level = $settings['level'];
        $logger->pushHandler(
            new ChannelLogStreamHandler(
                $channel,
                $path,
                $level
            )
        );
        return $logger;
    }
}