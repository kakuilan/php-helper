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
use Error;
use Exception;
use ReflectionException;


class ObjectsTest extends TestCase {


    /**
     * 基础对象测试
     * @throws ReflectionException
     */
    public function testBase() {
        $baseObj = new BaseCls();
        $baseObj->name = 'hello';
        $baseObj->gender = 0;

        $this->assertEquals(strval($baseObj), get_class($baseObj));
        $this->assertEquals($baseObj->getClassShortName(), 'BaseCls');
    }


    /**
     * 严格对象测试
     * @throws ReflectionException
     */
    public function testStrict() {
        $striObj = new StrictCls();
        $striObj->name = 'zhang3';
        $ref = $striObj->getReflectionObject();
        $this->assertTrue($ref instanceof ReflectionClass);

        try {
            $gender = $striObj->gender;
        }catch (Error $e) {
            var_dump('------------', $e);
        }

        //$nick = $striObj->nick;

        try {
            $id = $striObj->id;
        }catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined readable property')!==false);
        }

        // 测试调用setNick
//        $striObj->nick = 'nihao';
//
//        try {
//            $striObj->id = '123';
//        }catch (Exception $e) {
//            $this->assertTrue(stripos($e->getMessage(), 'Undefined writable property')!==false);
//        }
//
//        $json = json_encode($striObj);
//        $arr = json_decode($json);
//        $this->assertTrue(is_string($json));
//        $this->assertTrue(is_object($arr));


    }






}