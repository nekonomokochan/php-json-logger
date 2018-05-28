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
     * @var \Monolog\Logger
     */
    private $monologInstance;

    /**
     * Logger constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $file = dirname(__FILE__) . '/' . date('Y-m-d') . '.log';

        $formatter = new JsonFormatter();

        $stream = new StreamHandler($file, MonoLogger::INFO);
        $stream->setFormatter($formatter);

        $this->monologInstance = new MonoLogger('PhpJsonLogger');
        $this->monologInstance->pushHandler($stream);
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

        $this->monologInstance->addInfo('info', $testData);
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonologInstance()
    {
        return $this->monologInstance;
    }
}
