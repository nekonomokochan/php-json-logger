<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

/**
 * Class JsonFormatter
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class JsonFormatter extends BaseJsonFormatter
{
    /**
     * @param array $record
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        $formattedRecord = [
            'log_level'         => $record['level_name'],
            'message'           => $record['message'],
            'trace_id'          => $record['extra']['trace_id'],
            'file'              => $record['context']['file'],
            'line'              => $record['context']['line'],
            'context'           => $record['context'],
            'remote_ip_address' => $this->extractIp(),
            'user_agent'        => $this->extractUserAgent(),
            'datetime'          => $record['datetime']->format('Y-m-d H:i:s'),
            'timezone'          => $record['datetime']->getTimezone()->getName(),
        ];

        unset($formattedRecord['context']['file']);
        unset($formattedRecord['context']['line']);

        if (empty($record['extra']) === false) {
            $formattedRecord['extra'] = $record['extra'];
        }

        if (isset($record['context']['errors'])) {
            $formattedRecord['errors'] = $record['context']['errors'];
            unset($formattedRecord['context']['errors']);
        }

        $json = $this->toJson($this->normalize($formattedRecord), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }

    /**
     * @return string
     */
    private function extractIp()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            $ipList = explode(',', $ip);

            return $ipList[0];
        }

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '127.0.0.1';
    }

    /**
     * @return string
     */
    private function extractUserAgent()
    {
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return 'unknown';
    }
}
