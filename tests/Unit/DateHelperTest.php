<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/17
 * Time: 10:15
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\DateHelper;


class DateHelperTest extends TestCase {


    public function testSmartDatetime() {
        $now = time();

        $res1 = DateHelper::smartDatetime($now - 5);
        $res2 = DateHelper::smartDatetime($now - 65);
        $res3 = DateHelper::smartDatetime($now - 650);
        $res4 = DateHelper::smartDatetime($now - (60 * 24 * 7));
        $res5 = DateHelper::smartDatetime($now - (60 * 24 * 71));
        $res6 = DateHelper::smartDatetime($now - (60 * 24 * 701));

        var_dump($res1, $res2, $res3, $res4, $res5, $res6);


    }

}