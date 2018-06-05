# php-json-logger
LoggingLibrary for PHP. Output by JSON Format

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
