<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Monolog\Processor\IntrospectionProcessor;
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
     * @var int
     */
    private $logLevel;

    /**
     * @var \Monolog\Logger
     */
    private $monologInstance;

    /**
     * @var int
     */
    private $createdTime;

    /**
     * @var string
     */
    private $logFileName;

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

        $this->generateLogFileName($builder->getFileName());

        $this->logLevel = $builder->getLogLevel();

        $formatter = new JsonFormatter();

        $stream = new StreamHandler($this->logFileName, $this->logLevel);
        $stream->setFormatter($formatter);

        $this->monologInstance = new MonoLogger('PhpJsonLogger');
        $this->monologInstance->pushHandler($stream);

        $this->monologInstance->pushProcessor(new IntrospectionProcessor(
            $this->getLogLevel(),
            $builder->getSkipClassesPartials(),
            $builder->getSkipStackFramesCount()
        ));

        $this->monologInstance->pushProcessor(function ($record) {
            $record['extra']['trace_id'] = $this->getTraceId();
            $record['extra']['created_time'] = $this->getCreatedTime();

            return $record;
        });
    }

    /**
     * @param $message
     * @param $context
     */
    public function debug($message, $context = [])
    {
        $this->monologInstance->addDebug($message, $context);
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
     * @param $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->monologInstance->addNotice($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->monologInstance->addWarning($message, $context);
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function error(\Throwable $e, array $context = [])
    {
        $formattedTrace = $this->formatStackTrace($e);

        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $formattedTrace;

        $this->monologInstance->addError(get_class($e), $context);
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function critical(\Throwable $e, array $context = [])
    {
        $formattedTrace = $this->formatStackTrace($e);

        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $formattedTrace;

        $this->monologInstance->addCritical(get_class($e), $context);
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function alert(\Throwable $e, array $context = [])
    {
        $formattedTrace = $this->formatStackTrace($e);

        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $formattedTrace;

        $this->monologInstance->addAlert(get_class($e), $context);
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function emergency(\Throwable $e, array $context = [])
    {
        $formattedTrace = $this->formatStackTrace($e);

        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $formattedTrace;

        $this->monologInstance->addEmergency(get_class($e), $context);
    }

    /**
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonologInstance()
    {
        return $this->monologInstance;
    }

    /**
     * @return int
     */
    public function getCreatedTime(): int
    {
        return $this->createdTime;
    }

    /**
     * @return string
     */
    public function getLogFileName(): string
    {
        return $this->logFileName;
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

    /**
     * @param string $fileName
     */
    private function generateLogFileName(string $fileName)
    {
        $format = '%s-%s.log';

        $this->logFileName = sprintf(
            $format,
            $fileName,
            date('Y-m-d')
        );
    }

    /**
     * @param \Throwable $e
     * @return array
     */
    private function formatStackTrace(\Throwable $e): array
    {
        $stackTrace = [];
        $i = 0;
        foreach ($e->getTrace() as $trace) {
            $format = sprintf(
                '#%s %s(%s): %s%s%s()',
                $i,
                $trace['file'] ?? '',
                $trace['line'] ?? '',
                $trace['class'] ?? '',
                $trace['type'] ?? '',
                $trace['function'] ?? ''
            );

            array_push(
                $stackTrace,
                $format
            );

            $i++;
        }

        return $stackTrace;
    }
}
