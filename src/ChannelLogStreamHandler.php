<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2017/4/8
 * Time: 17:52
 */

namespace Nickfan\ChannelLog;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Use channels to log into separate files
 *
 */
class ChannelLogStreamHandler extends StreamHandler
{
    /**
     * Channel name
     *
     * @var String
     */
    protected $channel;

    /**
     * @param String $channel Channel name to write
     * @param bool|int $stream
     * @param bool|int $level
     * @param bool $bubble
     * @param null $filePermission
     * @param bool $useLocking
     * @see parent __construct for params
     */
    public function __construct(
        $channel,
        $stream,
        $level = Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ) {
        $this->channel = $channel;

        $formatter = new LineFormatter(null, null, false, true);
        $this->setFormatter($formatter);

        parent::__construct($stream, $level, $bubble);
    }

    /**
     * When to handle the log record.
     *
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record)
    {
        //Handle if Level high enough to be handled (default mechanism)
        //AND CHANNELS MATCHING!
        if (isset($record['channel'])) {
            return ($record['level'] >= $this->level && $record['channel'] == $this->channel);
        }
        return ($record['level'] >= $this->level);
    }

}