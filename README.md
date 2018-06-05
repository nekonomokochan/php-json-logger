# php-json-logger
[![Latest Stable Version](https://poser.pugx.org/nekonomokochan/php-json-logger/v/stable)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![Total Downloads](https://poser.pugx.org/nekonomokochan/php-json-logger/downloads)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![Latest Unstable Version](https://poser.pugx.org/nekonomokochan/php-json-logger/v/unstable)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![License](https://poser.pugx.org/nekonomokochan/php-json-logger/license)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![Monthly Downloads](https://poser.pugx.org/nekonomokochan/php-json-logger/d/monthly)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![Daily Downloads](https://poser.pugx.org/nekonomokochan/php-json-logger/d/daily)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![composer.lock](https://poser.pugx.org/nekonomokochan/php-json-logger/composerlock)](https://packagist.org/packages/nekonomokochan/php-json-logger)
[![Build Status](https://travis-ci.org/nekonomokochan/php-json-logger.svg?branch=master)](https://travis-ci.org/nekonomokochan/php-json-logger)
[![Coverage Status](https://coveralls.io/repos/github/nekonomokochan/php-json-logger/badge.svg?branch=master)](https://coveralls.io/github/nekonomokochan/php-json-logger?branch=master)

LoggingLibrary for PHP. Output by JSON Format

This Library is mainly intended for use in web applications.

## Getting Started

### Install composer package

```
composer require nekonomokochan/php-json-logger
```

## How To Use

### Basic usage

```php
<?php
use Nekonomokochan\PhpJsonLogger\LoggerBuilder;

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
```

It is output as follows.

```json
{
    "log_level": "INFO",
    "message": "🐱",
    "trace_id": "35b627ce-55e0-4729-9da0-fbda2a7d817d",
    "file": "\/home\/vagrant\/php-json-logger\/tests\/LoggerTest.php",
    "line": 42,
    "context": {
        "title": "Test",
        "price": 4000,
        "list": [
            1,
            2,
            3
        ],
        "user": {
            "id": 100,
            "name": "keitakn"
        }
    },
    "remote_ip_address": "127.0.0.1",
    "user_agent": "unknown",
    "datetime": "2018-06-04 17:21:03.631409",
    "timezone": "Asia\/Tokyo",
    "process_time": 631.50811195373535
}
```

#### How to change output filepath

Default output filepath is `/tmp/php-json-logger-yyyy-mm-dd.log` .

If you want to change the output filepath, please set the output filepath to the builder class.

```php
<?php
use Nekonomokochan\PhpJsonLogger\LoggerBuilder;

$fileName = '/tmp/test-php-json-logger';

$context = [
    'cat'    => '🐱',
    'dog'    => '🐶',
    'rabbit' => '🐰',
];

$loggerBuilder = new LoggerBuilder();
$loggerBuilder->setFileName($fileName);
$logger = $loggerBuilder->build();
$logger->info('testSetLogFileName', $context);
```

The output filepath is `/tmp/test-php-json-logger-yyyy-mm-dd.log` .

It is output as follows.

```json
{
    "log_level": "INFO",
    "message": "testSetLogFileName",
    "trace_id": "20f39cdb-dbd8-470c-babd-093a2974d169",
    "file": "\/home\/vagrant\/php-json-logger\/tests\/LoggerTest.php",
    "line": 263,
    "context": {
        "cat": "🐱",
        "dog": "🐶",
        "rabbit": "🐰"
    },
    "remote_ip_address": "127.0.0.1",
    "user_agent": "unknown",
    "datetime": "2018-06-05 11:28:03.214995",
    "timezone": "Asia\/Tokyo",
    "process_time": 215.09790420532227
}
```

#### How to Set `trace_id`

Any value can be set for `trace_id`.

This will help you when looking for logs you want.

```php
<?php
use Nekonomokochan\PhpJsonLogger\LoggerBuilder;

$context = [
    'name' => 'keitakn',
];

$loggerBuilder = new LoggerBuilder();
$loggerBuilder->setTraceId('MyTraceID');
$logger = $loggerBuilder->build();
$logger->info('testSetTraceIdIsOutput', $context);
```

It is output as follows.

```json
{
    "log_level": "INFO",
    "message": "testSetTraceIdIsOutput",
    "trace_id": "MyTraceID",
    "file": "\/home\/vagrant\/php-json-logger\/tests\/LoggerTest.php",
    "line": 214,
    "context": {
        "name": "keitakn"
    },
    "remote_ip_address": "127.0.0.1",
    "user_agent": "unknown",
    "datetime": "2018-06-05 11:36:02.394269",
    "timezone": "Asia\/Tokyo",
    "process_time": 394.35911178588867
}
```

#### How to change logLevel

Please use `\Nekonomokochan\PhpJsonLogger\LoggerBuilder::setLogLevel()` .

For example, the following code does not output logs.

Because the level is set to `CRITICAL`.

```php
<?php
use Nekonomokochan\PhpJsonLogger\LoggerBuilder;

$context = [
    'cat'    => '🐱',
    'dog'    => '🐶',
    'rabbit' => '🐰',
];

$loggerBuilder = new LoggerBuilder();
$loggerBuilder->setLogLevel(LoggerBuilder::CRITICAL);
$logger = $loggerBuilder->build();
$logger->info('testSetLogLevel', $context);
```

You can set the following values for `logLevel` .

These are the same as `logLevel` defined in [Monolog](https://github.com/Seldaek/monolog).

- DEBUG = 100
- INFO = 200
- NOTICE = 250
- WARNING = 300
- ERROR = 400
- CRITICAL = 500
- ALERT = 550
- EMERGENCY = 600

## License
MIT
