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
        try {
            $str = 'hello';
            $a   = $str{9};
        } catch (Exception $e) {
            DebugHelper::errorLogHandler();
            error_clear_last();
        }

        @$c = file_get_contents('helloworld');
        DebugHelper::errorLogHandler();
        error_clear_last();

        $tmpDir = sys_get_temp_dir();
        $logFile = $tmpDir. '/phperr_' . date('Ymd') . '.log';
        $cont    = file_get_contents($logFile);
        $this->assertNotEmpty($cont);
    }

}