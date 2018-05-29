<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Ramsey\Uuid\Uuid;

/**
 * Class Logger
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class Logger
{
    /**
     * @var string
     */
    private $traceId;

    /**
     * @var \Monolog\Logger
     */
    private $monologInstance;

    /**
     * @var int
     */
    private $createdTime;

    /**
     * Logger constructor.
     *
     * @param LoggerBuilder $builder
     * @throws \Exception
     */
    public function __construct(LoggerBuilder $builder)
    {
        $this->createdTime = microtime(true);
        $this->traceId = $builder->getTraceId();

        $this->generateTraceIdIfNeeded();

        $file = dirname(__FILE__) . '/' . date('Y-m-d') . '.log';

        $formatter = new JsonFormatter();

        $stream = new StreamHandler($file, MonoLogger::INFO);
        $stream->setFormatter($formatter);

        $this->monologInstance = new MonoLogger('PhpJsonLogger');
        $this->monologInstance->pushHandler($stream);

        $this->monologInstance->pushProcessor(function ($record) {
            $record['extra']['trace_id'] = $this->traceId;
            $record['extra']['created_time'] = $this->createdTime;

            return $record;
        });
    }

    /**
     * @param $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $trace = debug_backtrace();
        $context['php_json_logger']['file'] = $trace[0]['file'];
        $context['php_json_logger']['line'] = $trace[0]['line'];

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

        $trace = debug_backtrace();
        $context['php_json_logger']['file'] = $trace[0]['file'];
        $context['php_json_logger']['line'] = $trace[0]['line'];

        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $stackTrace;

        $this->monologInstance->addError(get_class($e), $context);
    }

    /**
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonologInstance()
    {
        return $this->monologInstance;
    }

    /**
     * Generate if TraceID is empty
     */
    private function generateTraceIdIfNeeded()
    {
        if (empty($this->traceId)) {
            $this->traceId = Uuid::uuid4()->toString();
        }
    }
}
