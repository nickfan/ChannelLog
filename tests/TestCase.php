<?php

namespace Nickfan\ChannelLog\Tests;

use Nickfan\ChannelLog\ChannelLogWriterStandAlone;

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
        $result1 = $channelLogWriter->channel('default')->info('my test message {mykey1}',['mykey1'=>'myval1','aaa'=>'abc']);
        $this->assertEquals(true,$result1);

        $result2 = $channelLogWriter->channel('event')->error('my event message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);
        $this->assertEquals(true,$result2);

        $result3 = $channelLogWriter->daily($projectRoot.'/logs/mycustom.log')->debug('my custom message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);
        $this->assertEquals(true,$result3);

        $result4 = $channelLogWriter->direct($projectRoot.'/logs/mydirect.log')->debug('my direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);
        $this->assertEquals(true,$result4);

        $result5 = $channelLogWriter->direct([
            'name'=>'newdirect',
            'console'=>true,
            'path'=>$projectRoot.'/logs/newdirect.log',
            ])->debug('new direct message {mykey2}',['mykey2'=>'myval2','qqq'=>'qwe']);
        $this->assertEquals(true,$result5);
    }
}
