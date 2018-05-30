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
    /**
     * @test
     */
    public function outputInfoLog()
    {
        $testData = [
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
        $logger->info('🐱', $testData);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
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
}
