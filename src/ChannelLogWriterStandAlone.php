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
     * @param string $channel
     * @param null|string $configure
     * @return Logger
     */
    public function channel($channel,$configure=null){
        if(!isset($this->instances[$channel]) || empty($this->instances[$channel])){
            $this->instances[$channel] = $this->getLoggerInstanceBySettins($channel,$this->config[$channel]);
        }else{
            if(!is_null($configure) && is_callable($configure)){
                $this->setChannel($channel,$configure);
            }elseif($configure===true && isset($this->config[$channel])){
                $this->instances[$channel] = $this->getLoggerInstanceBySettins($channel,$this->config[$channel]);
            }
        }
        return $this->instances[$channel];
    }

    public function __call($func, $params)
    {
        if(empty($this->currentChannel)){
            throw new \InvalidArgumentException('set Current Channel First.');
        }
        return call_user_func_array([$this->instances[$this->currentChannel],$func],$params);
    }


    protected function getLoggerInstanceBySettins($channel,$settings=[])
    {
        $channelLoggerInstance = new Logger($channel);
        if(empty($settings) || !isset($settings['configurator'])){
            $configuratorClassName = ChannelLogDefaultConfigurator::class;
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

}