<?php

namespace Nickfan\ChannelLog\Tests;

use Monolog\Logger;
use Nickfan\ChannelLog\ChannelLogWriter;

class TestCase extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        require_once __DIR__.'/../vendor/autoload.php';
    }
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testDefault()
    {
        $projectRoot = dirname(__DIR__);
        $channelLogWriter = new ChannelLogWriter(
            [
                'default'=>[
                    'path'=>$projectRoot.'/logs/default.log',
                    'level'=>Logger::INFO,
                ],
                'event' => [
                    'path' => $projectRoot.'/logs/event.log',
                    'level' => \Monolog\Logger::DEBUG,
                ],
            ]
        );
        $channelLogWriter->channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);
        $channelLogWriter->channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);

        $this->assertEquals(1,1*1);
    }
}
