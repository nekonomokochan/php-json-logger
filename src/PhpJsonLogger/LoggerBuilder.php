<?php
namespace Nekonomokochan\PhpJsonLogger;

/**
 * Class LoggerBuilder
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
class LoggerBuilder
{
    /**
     * @var string
     */
    private $traceId;

    /**
     * @var string
     */
    private $fileName;

    /**
     * LoggerBuilder constructor.
     *
     * @param string $traceId
     */
    public function __construct(string $traceId = '')
    {
        $this->traceId = $traceId;
        $this->fileName = '/tmp/php-json-logger';
    }

    /**
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @param string $traceId
     */
    public function setTraceId(string $traceId)
    {
        $this->traceId = $traceId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return Logger
     * @throws \Exception
     */
    public function build()
    {
        return new Logger($this);
    }
}
