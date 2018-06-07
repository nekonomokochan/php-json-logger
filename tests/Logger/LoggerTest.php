<?php
namespace Nekonomokochan\Tests\Logger;

use Nekonomokochan\PhpJsonLogger\LoggerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpJsonLoggerTest
 *
 * Add common test code to this class for all log methods
 *
 * @package Nekonomokochan\Tests
 */
class LoggerTest extends TestCase
{
    /**
     * @var string
     */
    private $defaultOutputFileBaseName;

    /**
     * @var string
     */
    private $defaultOutputFileName;

    /**
     * Delete the log file used last time to test the contents of the log file
     */
    public function setUp()
    {
        parent::setUp();
        $this->defaultOutputFileBaseName = '/tmp/php-json-logger.log';
        $this->defaultOutputFileName = '/tmp/php-json-logger-' . date('Y-m-d') . '.log';

        if (file_exists($this->defaultOutputFileName)) {
            unlink($this->defaultOutputFileName);
        }
    }

    /**
     * @test
     */
    public function outputUserAgent()
    {
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

        $context = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('testOutputUserAgent', $context);

        unset($_SERVER['HTTP_USER_AGENT']);

        $resultJson = file_get_contents($this->defaultOutputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testOutputUserAgent',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 54,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => $userAgent,
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function outputRemoteIpAddress()
    {
        $remoteIpAddress = '192.168.10.20';
        $_SERVER['REMOTE_ADDR'] = $remoteIpAddress;

        $context = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('testOutputRemoteIpAddress', $context);

        unset($_SERVER['REMOTE_ADDR']);

        $resultJson = file_get_contents($this->defaultOutputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testOutputRemoteIpAddress',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 97,
            'context'           => $context,
            'remote_ip_address' => $remoteIpAddress,
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function setTraceIdIsOutput()
    {
        $context = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setTraceId('MyTraceID');
        $logger = $loggerBuilder->build();
        $logger->info('testSetTraceIdIsOutput', $context);

        $resultJson = file_get_contents($this->defaultOutputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testSetTraceIdIsOutput',
            'trace_id'          => 'MyTraceID',
            'file'              => __FILE__,
            'line'              => 138,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame('MyTraceID', $logger->getTraceId());
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function setLogFileName()
    {
        $outputFileBaseName = '/tmp/test-php-json-logger.log';
        $outputFileName = '/tmp/test-php-json-logger-' . date('Y-m-d') . '.log';
        if (file_exists($outputFileName)) {
            unlink($outputFileName);
        }

        $context = [
            'cat'    => '🐱',
            'dog'    => '🐶',
            'rabbit' => '🐰',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setFileName($outputFileBaseName);
        $logger = $loggerBuilder->build();
        $logger->info('testSetLogFileName', $context);

        $resultJson = file_get_contents($outputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testSetLogFileName',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 186,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame(
            $outputFileBaseName,
            $logger->getLogFileName()
        );
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function setLogLevel()
    {
        $context = [
            'cat'    => '🐱',
            'dog'    => '🐶',
            'rabbit' => '🐰',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setLogLevel(LoggerBuilder::CRITICAL);
        $logger = $loggerBuilder->build();
        $logger->info('testSetLogLevel', $context);

        $this->assertFalse(
            file_exists($this->defaultOutputFileName)
        );

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame(500, $logger->getLogLevel());
    }

    /**
     * @test
     */
    public function outputHttpXForwardedFor()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.0,10.1.1.1';
        $_SERVER['REMOTE_ADDR'] = '192.168.10.20';

        $expectedRemoteIpAddress = '10.0.0.0';

        $context = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $logger = $loggerBuilder->build();
        $logger->info('testOutputHttpXForwardedFor', $context);

        unset($_SERVER['REMOTE_ADDR']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $resultJson = file_get_contents($this->defaultOutputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testOutputHttpXForwardedFor',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 257,
            'context'           => $context,
            'remote_ip_address' => $expectedRemoteIpAddress,
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }

    /**
     * @test
     */
    public function canSetMaxFiles()
    {
        $context = [
            'name' => 'keitakn',
        ];

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setMaxFiles(2);
        $logger = $loggerBuilder->build();
        $logger->info('testSetTraceIdIsOutput', $context);

        $resultJson = file_get_contents($this->defaultOutputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'INFO',
            'message'           => 'testSetTraceIdIsOutput',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 299,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame(2, $logger->getMaxFiles());
        $this->assertSame($expectedLog, $resultArray);
    }
}
