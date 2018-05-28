<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

/**
 * Class Logger
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class Logger
{
    /**
     * @var MonoLogger
     */
    private $monoLogger;

    public function __construct()
    {
        $file = dirname(__FILE__) . '/' . date('Y-m-d') . '.log';

        $formatter = new JsonFormatter();

        $stream = new StreamHandler($file, MonoLogger::INFO);
        $stream->setFormatter($formatter);

        $this->monoLogger = new MonoLogger('PhpJsonLogger');
        $this->monoLogger->pushHandler($stream);
    }

    /**
     * Output info log
     */
    public function info()
    {
        $testData = [
            'ip'         => '192.168.10.10',
            'user_agent' => 'Mac',
        ];

        $this->monoLogger->info('info', $testData);
    }

    /**
     * @return MonoLogger
     */
    public function getMonoLogger()
    {
        return $this->monoLogger;
    }
}
