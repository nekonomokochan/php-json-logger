<?php
namespace Nekonomokochan\Tests\Logger;

use Nekonomokochan\PhpJsonLogger\LoggerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class NoticeTest
 *
 * @package Nekonomokochan\Tests\Logger
 * @see \Nekonomokochan\PhpJsonLogger\Logger::notice
 */
class NoticeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        // Delete the log file to assert the log file
        $defaultFile = '/tmp/php-json-logger-' . date('Y-m-d') . '.log';
        if (file_exists($defaultFile)) {
            unlink($defaultFile);
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function outputNoticeLog()
    {
        $context = [
            'title' => 'Test',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setLogLevel(LoggerBuilder::DEBUG);
        $logger = $loggerBuilder->build();
        $logger->notice('🐶', $context);

        $resultJson = file_get_contents('/tmp/php-json-logger-' . date('Y-m-d') . '.log');
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'NOTICE',
            'message'           => '🐶',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 38,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }
}
