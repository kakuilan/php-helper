<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/27
 * Time: 17:09
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\DebugHelper;

class DebugHelperTest extends TestCase {


    public function testErrorHandler() {
        try {
            trigger_error("A custom error has been triggered");
        } catch (Exception $e) {
            DebugHelper::errorLogHandler();
        }

        $logFile = '/tmp/phperr_' . date('Ymd') . '.log';
        $cont    = file_get_contents($logFile);
        $this->assertNotEmpty($cont);
    }

}