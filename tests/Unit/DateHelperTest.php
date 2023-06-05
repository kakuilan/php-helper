<?php

/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
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

    public function testTimestamp() {
        date_default_timezone_set('UTC');
        $now   = time();
        $str   = date('Y-m-d H:i:s', $now);
        $obj   = new \DateTime($str, (new \DateTimeZone('UTC')));
        $tests = [
            [null, $now],
            ['', $now],
            [false, 0],
            [0, 0],
            [true, 1],
            [-999, -999],
            [$now, $now],
            [$str, $now],
            [$obj, $now],
        ];
        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual      = DateHelper::timestamp($time);
            $this->assertGreaterThanOrEqual($expected, $actual);
        }
    }


    public function testSmartDatetime() {
        $now  = time();
        $res1 = DateHelper::smartDatetime($now - 5);
        $res2 = DateHelper::smartDatetime($now - 65);
        $res3 = DateHelper::smartDatetime($now - (3600 * 2));
        $res4 = DateHelper::smartDatetime($now - (3600 * 24 * 7));
        $res5 = DateHelper::smartDatetime($now - (3600 * 24 * 71));
        $res6 = DateHelper::smartDatetime($now - (3600 * 24 * 701));

        $this->assertEquals('刚刚', $res1);
        $this->assertGreaterThan(0, mb_stripos($res2, '分钟前'));
        $this->assertGreaterThan(0, mb_stripos($res3, '小时前'));
        $this->assertGreaterThan(0, mb_stripos($res4, '天前'));
        $this->assertGreaterThan(0, mb_stripos($res5, '月前'));
        $this->assertGreaterThan(0, strtotime($res6));
    }


    public function testGetMonthDays() {
        $res1 = DateHelper::getMonthDays();
        $res2 = DateHelper::getMonthDays(1);
        $res3 = DateHelper::getMonthDays(13);
        $res4 = DateHelper::getMonthDays(2, 1900);
        $res5 = DateHelper::getMonthDays(2, 2000);
        $res6 = DateHelper::getMonthDays(2, 2020);
        $res7 = DateHelper::getMonthDays(2, 2019);

        $this->assertGreaterThan(1, $res1);
        $this->assertEquals(31, $res2);
        $this->assertEquals(0, $res3);
        $this->assertEquals(28, $res4);
        $this->assertEquals(29, $res5);
        $this->assertEquals(29, $res6);
        $this->assertEquals(28, $res7);
    }


    public function testSecond2time() {
        $res1 = DateHelper::second2time(0);
        $res2 = DateHelper::second2time(10);
        $res3 = DateHelper::second2time(120);
        $res4 = DateHelper::second2time(3611);
        $res5 = DateHelper::second2time(370211);

        $this->assertEmpty($res1);
        $this->assertEquals('00:10', $res2);
        $this->assertEquals('02:00', $res3);
        $this->assertEquals('01:00:11', $res4);
        $this->assertEquals('102:50:11', $res5);
    }


    public function testGetMicrosecond() {
        $res = DateHelper::getMicrosecond();
        $len = strlen($res);
        $this->assertGreaterThan(0, $res);
        $this->assertLessThanOrEqual(6, $len);
    }


    public function testGetMillitime() {
        $res = DateHelper::getMillitime();
        $len = strlen($res);
        $this->assertGreaterThan(0, $res);
        $this->assertEquals(13, $len);
    }


    public function testGetXingZuo() {
        $time  = 1582368688;
        $tests = [
            [123456, ''],
            [$time, '双鱼'],
            ['hello', ''],
            ['2020-01-12 18:51:27', '摩羯'],
            ['2020-01-22 18:51:27', '水瓶'],
            ['2020-02-12 18:51:27', '水瓶'],
            ['2020-02-22 18:51:27', '双鱼'],
            ['2020-03-12 18:51:27', '双鱼'],
            ['2020-03-22 18:51:27', '白羊'],
            ['2020-04-12 18:51:27', '白羊'],
            ['2020-04-22 18:51:27', '金牛'],
            ['2020-05-12 18:51:27', '金牛'],
            ['2020-05-22 18:51:27', '双子'],
            ['2020-06-12 18:51:27', '双子'],
            ['2020-06-22 18:51:27', '巨蟹'],
            ['2020-07-22 18:51:27', '巨蟹'],
            ['2020-07-23 18:51:27', '狮子'],
            ['2020-08-12 18:51:27', '狮子'],
            ['2020-08-24 18:51:27', '处女'],
            ['2020-09-12 18:51:27', '处女'],
            ['2020-09-25 18:51:27', '天秤'],
            ['2020-10-12 18:51:27', '天秤'],
            ['2020-10-27 18:51:27', '天蝎'],
            ['2020-11-12 18:51:27', '天蝎'],
            ['2020-11-22 18:51:27', '射手'],
            ['2020-12-12 18:51:27', '射手'],
            ['2020-12-22 18:51:27', '摩羯'],
        ];

        foreach ($tests as $test) {
            $expected = $test[1];
            $actual = DateHelper::getXingZuo($test[0]);
            $this->assertEquals($expected, $actual);
        }
    }


    public function testGetShengXiao() {
        $time  = 1582368688;
        $tests = [
            ['hello', ''],
            [$time, '鼠'],
            ['1900', '鼠'],
            ['1901', '牛'],
            ['1902', '虎'],
            ['1903', '兔'],
            ['1904', '龙'],
            ['1905', '蛇'],
            ['1906', '马'],
            ['1907', '羊'],
            ['1908', '猴'],
            ['1909', '鸡'],
            ['1910', '狗'],
            ['1911', '猪'],
            ['1912', '鼠'],
            ['2020-02-22', '鼠'],
        ];

        foreach ($tests as $test) {
            $expected = $test[1];
            $actual = DateHelper::getShengXiao($test[0]);
            $this->assertEquals($expected, $actual);
        }
    }


    public function testGetLunarYear() {
        $time  = 1582368688;
        $tests = [
            ['hello', ''],
            [$time, '庚子'],
            ['2020', '庚子'],
            ['2019-02-05', '己亥'],
            ['2018', '戊戌'],
            ['2017', '丁酉'],
            ['2016', '丙申'],
        ];

        foreach ($tests as $test) {
            $expected = $test[1];
            $actual   = DateHelper::getLunarYear($test[0]);
            $this->assertEquals($expected, $actual);
        }
    }


    public function testStartOfHour() {
        date_default_timezone_set('UTC');
        $tests = [
            [-1, '1969-12-31 23:00:00'],
            [0, '1970-01-01 00:00:00'],
            [null, date('Y-m-d H:00:00')],
            [strtotime('2020-01-12 18:51:27'), '2020-01-12 18:00:00'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-10 23:00:00'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-06 22:00:00'],
            [strtotime('2020-08-27 22:45:49'), '2020-08-27 22:00:00'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::startOfHour($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testEndOfHour() {
        date_default_timezone_set('UTC');
        $tests = [
            [-1, '1969-12-31 23:59:59'],
            [0, '1970-01-01 00:59:59'],
            [null, date('Y-m-d H:59:59')],
            [strtotime('2020-01-12 18:51:27'), '2020-01-12 18:59:59'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-10 23:59:59'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-06 22:59:59'],
            [strtotime('2020-08-27 22:45:49'), '2020-08-27 22:59:59'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::endOfHour($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testStartOfDay() {
        $tests = [
            [-1, '1969-12-31 00:00:00'],
            [0, '1970-01-01 00:00:00'],
            [null, date('Y-m-d 00:00:00')],
            [strtotime('2020-01-12 18:51:27'), '2020-01-12 00:00:00'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-10 00:00:00'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-06 00:00:00'],
            [strtotime('2020-08-27 22:45:49'), '2020-08-27 00:00:00'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::startOfDay($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testEndOfDay() {
        $tests = [
            [-1, '1969-12-31 23:59:59'],
            [0, '1970-01-01 23:59:59'],
            [null, date('Y-m-d 23:59:59')],
            [strtotime('2020-01-12 18:51:27'), '2020-01-12 23:59:59'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-10 23:59:59'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-06 23:59:59'],
            [strtotime('2020-08-27 22:45:49'), '2020-08-27 23:59:59'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::endOfDay($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testStartOfMonth() {
        $tests = [
            [-1, '1969-12-01 00:00:00'],
            [0, '1970-01-01 00:00:00'],
            [null, date('Y-m-01 00:00:00')],
            [strtotime('2020-01-12 18:51:27'), '2020-01-01 00:00:00'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-01 00:00:00'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-01 00:00:00'],
            [strtotime('2020-08-27 22:45:49'), '2020-08-01 00:00:00'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::startOfMonth($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testEndOfMonth() {
        $tests = [
            [-1, '1969-12-31 23:59:59'],
            [0, '1970-01-31 23:59:59'],
            [null, null],
            [strtotime('2020-01-12 18:51:27'), '2020-01-31 23:59:59'],
            [strtotime('2020-03-10 23:04:35'), '2020-03-31 23:59:59'],
            [strtotime('2020-5-6 22:45:49'), '2020-05-31 23:59:59'],
            [strtotime('2020-06-27 22:45:49'), '2020-06-30 23:59:59'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::endOfMonth($time);
            if (!$expected) {
                $d   = DateHelper::getMonthDays(DateHelper::month($time), DateHelper::year($time));
                $fmt = "Y-m-{$d} 23:59:59";
                $this->assertEquals(date($fmt), date('Y-m-d H:i:s', $actual));
            } else {
                $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
            }
        }
    }


    public function testStartOfYear() {
        $tests = [
            [-1, '1969-01-01 00:00:00'],
            [0, '1970-01-01 00:00:00'],
            [null, date('Y-01-01 00:00:00')],
            [strtotime('2017-01-12 18:51:27'), '2017-01-01 00:00:00'],
            [strtotime('2018-03-10 23:04:35'), '2018-01-01 00:00:00'],
            [strtotime('2019-5-6 22:45:49'), '2019-01-01 00:00:00'],
            [strtotime('2020-08-27 22:45:49'), '2020-01-01 00:00:00'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::startOfYear($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testEndOfYear() {
        $tests = [
            [-1, '1969-12-31 23:59:59'],
            [0, '1970-12-31 23:59:59'],
            [null, date('Y-12-31 23:59:59')],
            [strtotime('2017-01-12 18:51:27'), '2017-12-31 23:59:59'],
            [strtotime('2018-03-10 23:04:35'), '2018-12-31 23:59:59'],
            [strtotime('2019-5-6 22:45:49'), '2019-12-31 23:59:59'],
            [strtotime('2020-08-27 22:45:49'), '2020-12-31 23:59:59'],
        ];

        foreach ($tests as $test) {
            $time     = $test[0];
            $expected = $test[1];
            $actual   = DateHelper::endOfYear($time);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testStartOfWeek() {
        $tests = [
            [strtotime('2020-1-2 22:45:49'), 1, '2019-12-30 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 2, '2019-12-31 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 3, '2020-01-01 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 4, '2020-01-02 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 5, '2019-12-27 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 6, '2019-12-28 00:00:00'],
            [strtotime('2020-1-2 22:45:49'), 7, '2019-12-29 00:00:00'],
        ];

        foreach ($tests as $test) {
            $time         = $test[0];
            $weekStartDay = $test[1];
            $expected     = $test[2];
            $actual       = DateHelper::startOfWeek($time, $weekStartDay);
            $this->assertEquals($weekStartDay, date('N', $actual));
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testEndOfWeek() {
        $tests = [
            [strtotime('2020-1-2 22:45:49'), 1, '2020-01-05 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 2, '2020-01-06 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 3, '2020-01-07 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 4, '2020-01-08 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 5, '2020-01-02 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 6, '2020-01-03 23:59:59'],
            [strtotime('2020-1-2 22:45:49'), 7, '2020-01-04 23:59:59'],
        ];

        foreach ($tests as $test) {
            $time         = $test[0];
            $weekStartDay = $test[1];
            $expected     = $test[2];
            $actual       = DateHelper::endOfWeek($time, $weekStartDay);
            $this->assertEquals($expected, date('Y-m-d H:i:s', $actual));
        }
    }


    public function testIsBetween() {
        $tests = [
            ['2023-02-01', '2023-01-01', '2023-03-01', true],
            ['2022-02-01', '2023-01-01', '2023-03-01', false],
            ['2023-02-01', '2023-01-01', null, true],
            ['2023-02-01', null, '2023-03-01', false],
            ['2023-02-13', '2023-03-01', null, false],
            ['2023-05-04', '2023-01-01', null, true],
            ['2023-05-05', null, null, false],
        ];

        foreach ($tests as $key => $test) {
            $time     = $test[0];
            $start    = $test[1];
            $end      = $test[2];
            $expected = $test[3];
            $result   = DateHelper::isBetween($time, $start, $end);
            $this->assertEquals($expected, $result, '[' . $key . '] 不符预期' . print_r($test, true));
        }
    }
}
