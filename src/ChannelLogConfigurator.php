<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2017/4/14
 * Time: 15:33
 */

namespace Nickfan\ChannelLog;


use Monolog\Logger;

interface ChannelLogConfigurator
{
    public function configure(Logger $logger, $channel, $settings=[]);
}