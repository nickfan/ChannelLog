<?php
namespace Nickfan\ChannelLog;
/**
 * Description
 *
 * @project ChannelLog
 * @package ChannelLog
 * @author nickfan <nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-09-24 10:41
 *
 */

use Illuminate\Support\ServiceProvider;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class ChannelLogServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        //
        $source = dirname(__DIR__).'/config/channel-log.php';

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('channel-log.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('channel-log');
        }

        $this->mergeConfigFrom($source, 'channel-log');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //$configPath = __DIR__ . '/../config/lara-spec-log.php';
        //$this->mergeConfigFrom($configPath, 'lara-spec-log');

        $this->app->singleton('channel-log',function($app){
            return new ChannelLogWriter($app['config']->get('channel-log',[
                'default' => [
                    'path' => 'logs/default.log',
                    'level' => \Monolog\Logger::DEBUG,
                ],
            ]));
        });
    }

}