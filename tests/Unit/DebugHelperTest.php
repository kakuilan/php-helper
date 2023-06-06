<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/27
 * Time: 17:09
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Throwable;
use Kph\Helpers\DebugHelper;
use Kph\Tests\Objects\BaseCls;
use Kph\Tests\Objects\StrictCls;

class DebugHelperTest extends TestCase {


    public function testErrorHandler() {
        $logFile = TESTDIR . 'phperr_' . date('Ymd') . '.log';
        try {
            $str = 'hello';
            $a   = $str + 8;
        } catch (Throwable $e) {
            //error_clear_last();
            //DebugHelper::errorLogHandler($logFile);
        }

        @$c = file_get_contents('helloworld');
        DebugHelper::errorLogHandler($logFile);

        $this->assertTrue(file_exists($logFile));
    }

}