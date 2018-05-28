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
     * @param \Throwable $e
     * @param array $context
     */
    public function error(\Throwable $e, array $context = [])
    {
        $stackTrace = [];
        $i = 0;
        foreach ($e->getTrace() as $trace) {
            $format = sprintf(
                '#%s %s(%s): %s%s%s()',
                $i,
                $trace['file'],
                $trace['line'],
                $trace['class'],
                $trace['type'],
                $trace['function']
            );

            array_push(
                $stackTrace,
                $format
            );

            $i++;
        }

        $context['errors']['message'] = $e->getMessage();
        $context['errors']['code'] = $e->getCode();
        $context['errors']['file'] = $e->getFile();
        $context['errors']['line'] = $e->getLine();
        $context['errors']['trace'] = $stackTrace;

        $this->monologInstance->addError(get_class($e), $context);
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonologInstance()
    {
        return $this->monologInstance;
    }
}
