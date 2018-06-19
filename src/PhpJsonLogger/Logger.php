<?php
namespace Nekonomokochan\PhpJsonLogger;

use Ramsey\Uuid\Uuid;

/**
 * Class Logger
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class Logger
{
    use ErrorsContextFormatter;

    use MonologInstanceCreator;

    /**
     * @var string
     */
    private $traceId;

    /**
     * @var string
     * @see \Monolog\Logger::$name
     */
    private $channel;

    /**
     * @var int
     */
    private $logLevel;

    /**
     * @var \Monolog\Logger
     */
    private $monologInstance;

    /**
     * @var string
     */
    private $logFileName;

    /**
     * @var int
     * @see \Monolog\Handler\RotatingFileHandler::$maxFiles
     */
    private $maxFiles;

    /**
     * Logger constructor.
     *
     * @param LoggerBuilder $builder
     * @throws \Exception
     */
    public function __construct(LoggerBuilder $builder)
    {
        $this->traceId = $builder->getTraceId();
        $this->generateTraceIdIfNeeded();
        $this->channel = $builder->getChannel();
        $this->logFileName = $builder->getFileName();
        $this->logLevel = $builder->getLogLevel();
        $this->maxFiles = $builder->getMaxFiles();

        $this->monologInstance = $this->createMonologInstance($this->traceId, $builder);
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
        $this->monologInstance->addError(get_class($e), $this->formatPhpJsonLoggerErrorsContext($e, $context));
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function critical(\Throwable $e, array $context = [])
    {
        $this->monologInstance->addCritical(get_class($e), $this->formatPhpJsonLoggerErrorsContext($e, $context));
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function alert(\Throwable $e, array $context = [])
    {
        $this->monologInstance->addAlert(get_class($e), $this->formatPhpJsonLoggerErrorsContext($e, $context));
    }

    /**
     * @param \Throwable $e
     * @param array $context
     */
    public function emergency(\Throwable $e, array $context = [])
    {
        $this->monologInstance->addEmergency(get_class($e), $this->formatPhpJsonLoggerErrorsContext($e, $context));
    }

    /**
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
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
     * @return string
     */
    public function getLogFileName(): string
    {
        return $this->logFileName;
    }

    /**
     * @return int
     */
    public function getMaxFiles(): int
    {
        return $this->maxFiles;
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
