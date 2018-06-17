<?php
namespace Nekonomokochan\Tests\Logger;

use Nekonomokochan\PhpJsonLogger\LoggerBuilder;
use Nekonomokochan\PhpJsonLogger\SlackHandlerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class SlackNotificationTest
 *
 * @package Nekonomokochan\Tests\Logger
 */
class SlackNotificationTest extends TestCase
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
        $this->outputFileBaseName = '/tmp/slack-log-test.log';
        $this->outputFileName = '/tmp/slack-log-test-' . date('Y-m-d') . '.log';

        if (file_exists($this->outputFileName)) {
            unlink($this->outputFileName);
        }
    }

    /**
     * @test
     */
    public function notificationToSlack()
    {
        $exception = new \Exception('TestException', 500);
        $context = [
            'name'  => 'keitakn',
            'email' => 'dummy@email.com',
        ];

        $slackToken = getenv('PHP_JSON_LOGGER_SLACK_TOKEN', true) ?: getenv('PHP_JSON_LOGGER_SLACK_TOKEN');
        $slackChannel = getenv('PHP_JSON_LOGGER_SLACK_CHANNEL', true) ?: getenv('PHP_JSON_LOGGER_SLACK_CHANNEL');

        $slackHandlerBuilder = new SlackHandlerBuilder($slackToken, $slackChannel);
        $slackHandlerBuilder->setLevel(LoggerBuilder::CRITICAL);

        $loggerBuilder = new LoggerBuilder();
        $loggerBuilder->setFileName($this->outputFileBaseName);
        $loggerBuilder->setSlackHandler($slackHandlerBuilder->build());
        $logger = $loggerBuilder->build();
        $logger->critical($exception, $context);

        $resultJson = file_get_contents($this->outputFileName);
        $resultArray = json_decode($resultJson, true);

        echo "\n ---- Output Log Begin ---- \n";
        echo json_encode($resultArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n ---- Output Log End   ---- \n";

        $expectedLog = [
            'log_level'         => 'CRITICAL',
            'message'           => 'Exception',
            'channel'           => 'PhpJsonLogger',
            'trace_id'          => $logger->getTraceId(),
            'file'              => __FILE__,
            'line'              => 60,
            'context'           => $context,
            'remote_ip_address' => '127.0.0.1',
            'user_agent'        => 'unknown',
            'datetime'          => $resultArray['datetime'],
            'timezone'          => date_default_timezone_get(),
            'process_time'      => $resultArray['process_time'],
            'errors'            => [
                'message' => 'TestException',
                'code'    => 500,
                'file'    => __FILE__,
                'line'    => 44,
                'trace'   => $resultArray['errors']['trace'],
            ],
        ];

        $this->assertSame('PhpJsonLogger', $logger->getMonologInstance()->getName());
        $this->assertSame($expectedLog, $resultArray);
    }
}
