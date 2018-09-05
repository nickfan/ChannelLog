<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2018/9/5
 * Time: 15:13
 */

namespace Nickfan\ChannelLog;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class ChannelLogJsonFormatter extends BaseJsonFormatter
{
    public function format(array $record)
    {
        // 这个就是最终要记录的数组，最后转成Json并记录进日志
        $newRecord = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'message' => $record['message'],
        ];

        if (isset($record['context']) && !empty($record['context'])) {
            $newRecord = array_merge($newRecord, $record['context']);
        }
        $json = $this->toJson($this->normalize($newRecord), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }
}
