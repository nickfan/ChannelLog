<?php
/**
 * Description
 *
 * @project ChannelLog
 * @package ChannelLog
 * @author nickfan <nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-09-24 11:41
 *
 */

namespace Nickfan\ChannelLog;

use Monolog\Logger;

class ChannelLogWriterStandAlone
{

    const CHANNEL_DEFAULT = 'default';

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    protected $config = [];
    protected $instances = [];
    protected $currentChannel = '';
    public function __construct(array $config=[]){
        $this->config +=$config;
    }

    public function initialize(){
        if(!empty($this->config)){
            foreach ($this->config  as $channelKey=>$channelSettings) {
                $this->instances[$channelKey] = null;
            }
        }
        return $this;
    }

    /**
     * @param array $config
     * @return ChannelLogWriter
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setChannel($channel,$configure=null){
        if(!is_null($configure) && is_callable($configure)){
            $loggerInstance = new Logger($channel);
            $loggerInstance = call_user_func($configure,$loggerInstance);
            $this->instances[$channel] = $loggerInstance;
        }
        return $this;
    }

    public function useChannel($channel,$configure=null){
        if(isset($this->config[$channel])){
            $this->channel($channel,$configure);
        }
        if(isset($this->instances[$channel]) && !empty($this->instances[$channel])){
            $this->currentChannel = $channel;
        }else{
            if(!is_null($configure) && is_callable($configure)){
                $this->setChannel($channel,$configure);
                $this->currentChannel = $channel;
            }else{
                throw new \InvalidArgumentException('Invalid channel used.');
            }
        }
        return $this;
    }

    /**
     * @param mixed|null $params
     * @return array
     */
    public static function parseSettingsByParams($params=null){
        $settings = [
            'name'=> 'console',
            'log' => 'console',
            'console'=> true,
            'path' => 'php://stdout',
            'level' => \Monolog\Logger::DEBUG
        ];
        if(!empty($params)) {
            if (is_array($params)) {
                $settings = $params;
                if(!isset($settings['log'])){
                    if(isset($settings['path']) && !empty($settings['path']) && $settings['path']!='php://stdout'){
                        $settings['log'] = 'single';
                    }else{
                        $settings['log'] = 'console';
                        $settings['path'] = 'php://stdout';
                    }
                }
                $settings = array_merge([
                    'name' => 'console',
                    'log' => 'console',
                    'console' => false,
                    'path' => 'php://stdout',
                    'level' => \Monolog\Logger::DEBUG
                ], $settings);
            } elseif (is_scalar($params)) {
                $settings = [
                    'name'=> basename($params),
                    'log' => 'single',
                    'console'=> true,
                    'path' => $params,
                    'level' => \Monolog\Logger::DEBUG
                ];
                if (strpos($params, '/') !== false
                    || strpos($params, '\\') !== false
                    || strpos($params, '.') !== false) {
                    $settings['console'] = false;
                }
            }
        }
        $settings['name'] = isset($settings['name']) && !empty($settings['name']) ? basename($settings['name'],'.'.pathinfo($settings['name'],PATHINFO_EXTENSION)) : md5($settings['path']);
        return $settings;
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function single($params=null){
        $settings = self::parseSettingsByParams($params);
        $settings['log']='single';
        return $this->direct($settings);
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function daily($params=null){
        $settings = self::parseSettingsByParams($params);
        $settings['log']='daily';
        return $this->direct($settings);
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function syslog($params=null){
        $settings = self::parseSettingsByParams($params);
        $settings['log']='syslog';
        return $this->direct($settings);
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function errorlog($params=null){
        $settings = self::parseSettingsByParams($params);
        $settings['log']='errorlog';
        return $this->direct($settings);
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function console($params=null){
        $settings = self::parseSettingsByParams($params);
        $settings['log']='console';
        return $this->direct($settings);
    }

    /**
     * @param mixed $params
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function direct($params=null){
        $settings = self::parseSettingsByParams($params);
        $channel = $settings['name'];
        if(!isset($this->instances[$channel]) || empty($this->instances[$channel])){
            $this->instances[$channel] = $this->getLoggerInstanceBySettings($channel,$settings);
        }
        return $this->instances[$channel];
    }

    /**
     * @param string $channel
     * @param null|string $configure
     * @return \Monolog\Logger
     * @throws \InvalidArgumentException
     */
    public function channel($channel,$configure=null){
        if(!isset($this->instances[$channel]) || empty($this->instances[$channel])){
            if(!is_null($configure) && is_callable($configure)){
                $this->setChannel($channel,$configure);
            }elseif(isset($this->config[$channel])){
                $this->instances[$channel] = $this->getLoggerInstanceBySettings($channel,$this->config[$channel]);
            }else{
                throw new \InvalidArgumentException('Invalid channel used.');
            }
        }else{
            if(!is_null($configure) && is_callable($configure)){
                $this->setChannel($channel,$configure);
            }elseif($configure===true && isset($this->config[$channel])){
                $this->instances[$channel] = $this->getLoggerInstanceBySettings($channel,$this->config[$channel]);
            }
        }
        return $this->instances[$channel];
    }

    protected function getLoggerInstanceBySettings($channel,$settings=[])
    {
        $channelLoggerInstance = new Logger($channel);
        if(empty($settings) || !isset($settings['configurator'])){
            $configuratorClassName = ChannelLogDefaultConfiguratorStandAlone::class;
        }else{
            $configuratorClassName = $settings['configurator'];
        }
        $configuratorClassInstance = new $configuratorClassName();
        if($configuratorClassInstance instanceof ChannelLogConfigurator){
            $channelLoggerInstance = call_user_func([$configuratorClassInstance,'configure'],$channelLoggerInstance, $channel, $settings);
        }else{
            throw new \InvalidArgumentException('Invalid configurator , must implements :'.ChannelLogConfigurator::class);
        }
        return $channelLoggerInstance;
    }

    public function __call($func, $params)
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],$func],$params);
    }

    /*
            'debug'     => Logger::DEBUG,
            'info'      => Logger::INFO,
            'notice'    => Logger::NOTICE,
            'warning'   => Logger::WARNING,
            'error'     => Logger::ERROR,
            'critical'  => Logger::CRITICAL,
            'alert'     => Logger::ALERT,
            'emergency' => Logger::EMERGENCY,
     */

    /**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  mixed   $level   The log level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function log($level, $message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function debug($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function info($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function notice($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function warn($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function warning($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function err($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function error($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function crit($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function critical($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function alert($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function emerg($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function emergency($message, array $context = array())
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],__FUNCTION__],func_get_args());
    }

}