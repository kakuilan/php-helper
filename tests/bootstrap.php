<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 15:52
 * Desc:
 */

define('DS', str_replace('\\', '/', DIRECTORY_SEPARATOR));
define('PS', PATH_SEPARATOR);
define('TESTDIR', str_replace('\\', '/', __DIR__ . DS)); //根目录
error_reporting(E_ALL);
ini_set('display_errors', 0);

$loader = require __DIR__ . '/../vendor/autoload.php';
$logFile = TESTDIR . 'tmp/phperr_' . date('Ymd') . '.log';
register_shutdown_function('\Kph\Helpers\DebugHelper::errorLogHandler', $logFile);