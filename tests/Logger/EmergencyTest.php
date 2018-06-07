<?php
namespace Nekonomokochan\Tests\Logger;

use Nekonomokochan\PhpJsonLogger\LoggerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class EmergencyTest
 *
 * @package Nekonomokochan\Tests\Logger
 * @see \Nekonomokochan\PhpJsonLogger\Logger::emergency
 */
class EmergencyTest extends TestCase
{
    /**
     * @var string
     */
    private $outputFileBaseName;

    /**
     * @var string
     */
    private $outputFileName;

    /**
     * Delete the log file used last time to test the contents of the log file
     */
    public function setUp()
    {
        parent::setUp();
        $this->outputFileBaseName = '/tmp/emergency-log-test.log';
        $this->outputFileName = '/tmp/emergency-log-test-' . date('Y-m-d') . '.log';

        if (file_exists($this->outputFileName)) {
            unlink($this->outputFileName);
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function outputEmergencyLog()
    {
        $exception = new \ErrorException('TestEmergency', 500);
        $context = [
            'name'  => 'keitakn',
            'email' => 'dummy@email.com',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setFileName($this->outputFileBaseName);
        $logger = $loggerBuilder->build();
        $logger->emergency($exception, $context);

        $resultJson = file_get_contents($this->outputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'EMERGENCY',
            'message'           => 'ErrorException',
            'channel'           => 'PhpJsonLogger',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 54,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
            'errors'            => [
                'message' => 'TestEmergency',
                'code'    => 500,
                'file'    => __FILE__,
                'line'    => 45,
                'trace'   => $resultArray['errors']['trace'],
            ],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }
}
