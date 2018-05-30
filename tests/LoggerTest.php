<?php
namespace Nekonomokochan\Tests;

use Nekonomokochan\PhpJsonLogger\LoggerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpJsonLoggerTest
 *
 * @package Nekonomokochan\Tests
 */
class LoggerTest extends TestCase
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
     */
    public function outputInfoLog()
    {
        $context = [
            'title' => 'Test',
            'price' => 4000,
            'list'  => [1, 2, 3],
            'user'  => [
                'id'   => 100,
                'name' => 'keitakn',
            ],
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('🐱', $context);

        $resultJson = file_get_contents('/tmp/php-json-logger-' . date('Y-m-d') . '.log');
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => '🐱',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 42,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => 'Asia/Tokyo',
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function outputErrorLog()
    {
        $exception = new \Exception('TestException', 500);
        $context = [
            'name'  => 'keitakn',
            'email' => 'dummy@email.com',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->error($exception, $context);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }

    /**
     * @test
     */
    public function outputUserAgent()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

        $testData = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('testOutputUserAgent', $testData);

        unset($_SERVER['HTTP_USER_AGENT']);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }

    /**
     * @test
     */
    public function outputRemoteIpAddress()
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.10.20';

        $testData = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('testOutputRemoteIpAddress', $testData);

        unset($_SERVER['REMOTE_ADDR']);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }

    /**
     * @test
     */
    public function setTraceIdIsOutput()
    {
        $testData = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setTraceId('MyTraceID');
        $logger = $loggerBuilder->build();
        $logger->info('testOutputRemoteIpAddress', $testData);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame('MyTraceID', $logger->getTraceId());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function setLogFileName()
    {
        $fileName = '/tmp/test-php-json-logger';

        $testData = [
            'cat'    => '🐱',
            'dog'    => '🐶',
            'rabbit' => '🐰',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setFileName($fileName);
        $logger = $loggerBuilder->build();
        $logger->info('testSetLogFileName', $testData);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame(
            '/tmp/test-php-json-logger-' . date('Y-m-d') . '.log',
            $logger->getLogFileName()
        );
    }

    /**
     * @test
     */
    public function setLogLevel()
    {
        $testData = [
            'cat'    => '🐱',
            'dog'    => '🐶',
            'rabbit' => '🐰',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setLogLevel(LoggerBuilder::CRITICAL);
        $logger = $loggerBuilder->build();
        $logger->info('testSetLogLevel', $testData);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame(500, $logger->getLogLevel());
    }
}
