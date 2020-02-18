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
        $this->assertEquals(6, $len);
    }


    public function testGetMillitime() {
        $res = DateHelper::getMillitime();
        $len = strlen($res);
        $this->assertGreaterThan(0, $res);
        $this->assertEquals(13, $len);
    }




}