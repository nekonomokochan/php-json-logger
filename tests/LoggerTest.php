<?php
namespace Nekonomokochan\Tests;

use Nekonomokochan\PhpJsonLogger\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpJsonLoggerTest
 *
 * @package Nekonomokochan\Tests
 */
class LoggerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testInfoSuccess()
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

        $logger = new Logger();
        $logger->info('🐱', $testData);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }

    /**
     * @throws \Exception
     */
    public function testOutputErrorLog()
    {
        $exception = new \Exception('TestException', 500);
        $context = [
            'name'  => 'keitakn',
            'email' => 'dummy@email.com',
        ];

        $logger = new Logger();
        $logger->error($exception, $context);

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }
}
