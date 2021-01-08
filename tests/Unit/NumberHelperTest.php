<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/25
 * Time: 16:32
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kph\Helpers\NumberHelper;
use Kph\Helpers\ValidateHelper;
use Kph\Exceptions\BaseException;
use Error;
use Exception;
use Throwable;


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
            $chk      = NumberHelper::inRange($expected, $test[0], $test[1]);
            $this->assertTrue($chk);
        }
    }


    public function testMoney2Yuan() {
        $num0 = 123456789087654;
        $num1 = 123456789876;
        $num2 = 12345678908.7;
        $num3 = 12345678908.76;
        $num4 = 12345678908.765;
        $num5 = 12345678908.7654;

        try {
            NumberHelper::money2Yuan($num0);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof BaseException);
        }

        $res1 = NumberHelper::money2Yuan($num1);
        $res2 = NumberHelper::money2Yuan($num2, 1);
        $res3 = NumberHelper::money2Yuan($num3, 2);
        $res4 = NumberHelper::money2Yuan($num4, 3);
        $res5 = NumberHelper::money2Yuan($num5, 5);
        $res6 = NumberHelper::money2Yuan(0.0, 5);

        $this->assertEquals($res1, '壹仟贰佰叁拾肆亿伍仟陆佰柒拾捌万玖仟捌佰柒拾陆元整');
        $this->assertEquals($res2, '壹佰贰拾叁亿肆仟伍佰陆拾柒万捌仟玖佰零捌元柒角整');
        $this->assertEquals($res3, '壹佰贰拾叁亿肆仟伍佰陆拾柒万捌仟玖佰零捌元柒角陆分整');
        $this->assertEquals($res4, '壹佰贰拾叁亿肆仟伍佰陆拾柒万捌仟玖佰零捌元柒角陆分伍厘整');
        $this->assertEquals($res4, $res5);
        $this->assertEquals($res6, '零元整');
    }


    public function testNearLogarithm() {
        $res1 = NumberHelper::nearLogarithm(1000, 10);
        $res2 = NumberHelper::nearLogarithm(1005, 10, true);
        $res3 = NumberHelper::nearLogarithm(1005, 10, false);

        $res4 = NumberHelper::nearLogarithm(8, 2, true);
        $res5 = NumberHelper::nearLogarithm(8, 2, false);

        $res6 = NumberHelper::nearLogarithm(14, 2, true);
        $res7 = NumberHelper::nearLogarithm(14, 2, false);

        $this->assertEquals($res1, 3);
        $this->assertEquals($res2, 3);
        $this->assertEquals($res3, 4);
        $this->assertEquals($res4, $res5);
        $this->assertEquals($res6, 3);
        $this->assertEquals($res7, 4);

        try {
            NumberHelper::nearLogarithm(-14, 2, false);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof BaseException);
        }
        try {
            NumberHelper::nearLogarithm(19, -2, false);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof BaseException);
        }
    }


    public function testSplitNaturalNum() {
        $res1 = NumberHelper::splitNaturalNum(15, 2);
        $res2 = NumberHelper::splitNaturalNum(36, 2);
        $res3 = NumberHelper::splitNaturalNum(37, 2);
        $res4 = NumberHelper::splitNaturalNum(4, 2);

        $this->assertEquals(4, count($res1));
        $this->assertEquals(2, count($res2));
        $this->assertEquals(3, count($res3));
        $this->assertEquals(4, current($res4));

        try {
            NumberHelper::splitNaturalNum(-14, 2, false);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof BaseException);
        }
        try {
            NumberHelper::splitNaturalNum(19, -2, false);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof BaseException);
        }
    }


}