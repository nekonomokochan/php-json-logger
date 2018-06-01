<?php
namespace Nekonomokochan\PhpJsonLogger;

trait ErrorsContextFormat
{
    /**
     * @param \Throwable $e
     * @return array
     */
    protected function formatPhpJsonLoggerErrorsContext(\Throwable $e, array $context): array
    {
        $context['php_json_logger']['errors']['message'] = $e->getMessage();
        $context['php_json_logger']['errors']['code'] = $e->getCode();
        $context['php_json_logger']['errors']['file'] = $e->getFile();
        $context['php_json_logger']['errors']['line'] = $e->getLine();
        $context['php_json_logger']['errors']['trace'] = $this->formatStackTrace($e->getTrace());

        return $context;
    }

    /**
     * @param array $traces
     * @return array
     */
    protected function formatStackTrace(array $traces): array
    {
        $stackTrace = [];
        $length = count($traces);

        for ($i = 0; $i < $length; $i++) {
            $format = sprintf(
                '#%s %s(%s): %s%s%s()',
                $i,
                $traces[$i]['file'] ?? '',
                $traces[$i]['line'] ?? '',
                $traces[$i]['class'] ?? '',
                $traces[$i]['type'] ?? '',
                $traces[$i]['function'] ?? ''
            );

            $stackTrace[] = $format;
        }

        return $stackTrace;
    }
}
