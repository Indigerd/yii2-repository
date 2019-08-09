#!/usr/bin/env php
<?php
// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

// Composer
require(__DIR__ . '/../../vendor/autoload.php');


// Yii
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

// Bootstrap application
require(__DIR__ . '/config/bootstrap.php');

$config = require(__DIR__ . '/config/console.php');

$exitCode = (new yii\console\Application($config))->run();
exit($exitCode);
