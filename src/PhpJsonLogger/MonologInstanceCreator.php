<?php
namespace Nekonomokochan\PhpJsonLogger;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Trait MonologInstanceCreator
 *
 * @package Nekonomokochan\PhpJsonLogger
 */
trait MonologInstanceCreator
{
    /**
     * @param string $traceId
     * @param LoggerBuilder $loggerBuilder
     * @return \Monolog\Logger
     */
    public function createMonologInstance(string $traceId, LoggerBuilder $loggerBuilder)
    {
        $formatter = new JsonFormatter();

        $rotating = new RotatingFileHandler(
            $loggerBuilder->getFileName(),
            $loggerBuilder->getMaxFiles(),
            $loggerBuilder->getLogLevel()
        );
        $rotating->setFormatter($formatter);

        $handlers = [
            $rotating
        ];

        $introspection = new IntrospectionProcessor(
            $loggerBuilder->getLogLevel(),
            $loggerBuilder->getSkipClassesPartials(),
            $loggerBuilder->getSkipStackFramesCount()
        );

        $extraRecords = function ($record) use ($traceId) {
            $record['extra']['trace_id'] = $traceId;
            $record['extra']['created_time'] = microtime(true);

            return $record;
        };

        $processors = [
            $introspection,
            $extraRecords,
        ];

        if ($loggerBuilder->getSlackHandler() instanceof SlackHandler) {
            $slack = $loggerBuilder->getSlackHandler();
            $slack->setFormatter($formatter);

            array_push(
                $handlers,
                $slack
            );

            $webProcessor = new WebProcessor();
            $webProcessor->addExtraField('server_ip_address', 'SERVER_ADDR');
            $webProcessor->addExtraField('user_agent', 'HTTP_USER_AGENT');
            array_push($processors, $webProcessor);
        }

        return new \Monolog\Logger(
            $loggerBuilder->getChannel(),
            $handlers,
            $processors
        );
    }
}
