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


    public function testGeoDistance() {
        $lat1 = 30.0;
        $lng1 = 45.0;
        $lat2 = 40.0;
        $lng2 = 90.0;

        $res1 = NumberHelper::geoDistance($lng1, $lat1, $lng2, $lat2);

        $lat1 = 390.0;
        $lng1 = 405.0;
        $lat2 = -320.0;
        $lng2 = 90.0;

        $res2 = NumberHelper::geoDistance($lng1, $lat1, $lng2, $lat2);

        $res3 = number_format($res1, 7, '.', '');
        $res4 = number_format($res2, 7, '.', '');

        $this->assertEquals('4199598.4916152', $res3);
        $this->assertEquals($res3, $res4);
    }


    public function testNumberFormat() {
        $num1 = 123000;
        $num2 = 1234.56789;

        $res1 = NumberHelper::numberFormat($num1);
        $res2 = NumberHelper::numberFormat($num2, 3);

        $this->assertEquals('123000.00', $res1);
        $this->assertEquals('1234.568', $res2);
    }


    public function testNumberSub() {
        $num1 = '123000';
        $num2 = 1234.56789;

        $res1 = NumberHelper::numberSub($num1, 0);
        $res2 = NumberHelper::numberSub($num2, 3);

        $this->assertEquals('123000', $res1);
        $this->assertEquals('1234.567', $res2);
    }


    public function testRandFloat() {
        $tests = [
            [0, 1],
            [1, 9],
            [-5, 5],
            [-1204, 6534],
        ];
        foreach ($tests as $test) {
            $expected = NumberHelper::randFloat($test[0], $test[1]);
            $chk = NumberHelper::inRange($expected, $test[0], $test[1]);
            $this->assertTrue($chk);
        }
    }

}