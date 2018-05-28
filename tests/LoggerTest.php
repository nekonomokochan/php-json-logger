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
        $logger = new Logger();
        $logger->info();

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
    }
}
