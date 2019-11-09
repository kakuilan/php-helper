<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:15
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kph\Tests\Objects\BaseCls;
use Kph\Tests\Objects\StrictCls;
use ReflectionClass;
use Exception;

class ObjectsTest extends TestCase {

    public function testBase() {
        $baseObj = new BaseCls();
        $baseObj->name = 'hello';

        $this->assertEquals(strval($baseObj), get_class($baseObj));
        $this->assertEquals($baseObj->getClassShortName(), 'BaseCls');

        $striObj = new StrictCls();
        $striObj->name = 'hehe';
        $ref = $striObj->getReflectionObject();
        $this->assertTrue($ref instanceof ReflectionClass);

        $nick = $striObj->nick;
        try {
            $id = $striObj->id;
        }catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined readable property')!==false);
        }

        $striObj->nick = 'nihao';
        try {
            $striObj->id = '123';
        }catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined writable property')!==false);
        }

        $json = json_encode($striObj);
        $arr = json_decode($json);
        $this->assertTrue(is_string($json));
        $this->assertTrue(is_object($arr));

    }



}