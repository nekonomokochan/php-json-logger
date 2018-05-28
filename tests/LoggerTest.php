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
     * info log test
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
}
