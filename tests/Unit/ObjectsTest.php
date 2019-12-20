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

        // 访问protected属性
        $gender = $striObj->get('gender');
        $this->assertEquals($gender, 'man');

        $nick = $striObj->get('nick');
        $this->assertEquals($nick, 'boot');

        // 访问private属性
        $id = $striObj->get('id');
        $this->assertEquals($id, 1);

        // 访问不存在的属性
        try {
            $none = $striObj->get('none');
        }catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined readable property')!==false);
        }

        // 设置protected属性
        $gender = 'woman';
        $striObj->set('gender', $gender);
        $this->assertEquals($gender, $striObj->get('gender'));

        $nick = 'hello';
        $striObj->set('nick', $nick);
        $this->assertEquals($nick, $striObj->get('nick'));

        // 设置private属性
        $id = 5;
        $striObj->set('id', $id);
        $this->assertEquals($id, $striObj->get('id'));

        // 设置不存在的属性
        try {
            $striObj->set('none', true);
        }catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined writable property')!==false);
        }

        // 销毁属性
        $key = 'name';
        $this->assertEquals(true, $striObj->isset($key));
        $striObj->unset($key);
        $this->assertEquals(false, $striObj->isset($key));
        $this->assertEquals(false, $striObj->isset('null'));

        // json化
        $json = json_encode($striObj);
        $arr = json_decode($json);
        $this->assertTrue(is_string($json));
        $this->assertTrue(is_object($arr));


    }






}