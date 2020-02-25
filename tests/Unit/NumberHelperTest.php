<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/25
 * Time: 16:32
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\NumberHelper;


class NumberHelperTest extends TestCase {

    public function testFormatBytes() {
        $res1 = NumberHelper::formatBytes(0);
        $res2 = NumberHelper::formatBytes(1024000, 2);
        $res3 = NumberHelper::formatBytes(1024000000, 3);
        $res4 = NumberHelper::formatBytes(1024000000000, 4);

        $this->assertEquals('0B', $res1);
        $this->assertEquals('1000KB', $res2);
        $this->assertEquals('976.563MB', $res3);
        $this->assertEquals('953.6743GB', $res4);
    }


    public function testInRange() {
        $res1 = NumberHelper::inRange(3, 9, 12);
        $res2 = NumberHelper::inRange(68, 12, 132);
        $res3 = NumberHelper::inRange(3.14159, 1.01, 8.003);

        $this->assertFalse($res1);
        $this->assertTrue($res2);
        $this->assertTrue($res3);
    }


    public function testSum() {
        $res1 = NumberHelper::sum(1, 3, 4, 6);
        $res2 = NumberHelper::sum(-1, 0.5, true, [], 4, 'hello', 231);

        $this->assertEquals(14, $res1);
        $this->assertEquals(234.5, $res2);
    }


    public function testAverage() {
        $res1 = NumberHelper::average(1, 3, 4, 6);
        $res2 = NumberHelper::average(-1, 0.5, true, [], 4, 'hello', 231);

        $this->assertEquals(3.5, $res1);
        $this->assertEquals(58.625, $res2);
    }



}