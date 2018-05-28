<?php
namespace Nekonomokochan\PhpJsonLogger;

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
     * @param $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->monologInstance->addInfo($message, $context);
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonologInstance()
    {
        return $this->monologInstance;
    }
}
