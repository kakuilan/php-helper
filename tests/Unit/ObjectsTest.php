<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:15
 * Desc:
 */

namespace Kph\Tests\Unit;

use Kph\Helpers\ValidateHelper;
use PHPUnit\Framework\TestCase;
usE Kph\Helpers\ArrayHelper;
use Kph\Objects\ArrayObject;
use Kph\Objects\BaseObject;
use Kph\Tests\Objects\BaseCls;
use Kph\Tests\Objects\StrictCls;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Error;
use Exception;
use Throwable;


class ObjectsTest extends TestCase {


    /**
     * 基础对象测试
     * @throws ReflectionException
     */
    public function testBase() {
        $baseObj         = new BaseCls();
        $baseObj->name   = 'hello';
        $baseObj->gender = 0;

        $this->assertEquals(strval($baseObj), get_class($baseObj));
        $this->assertEquals($baseObj::getShortName(), 'BaseCls');

        $arr1 = BaseCls::parseNamespacePath();
        $arr2 = BaseObject::parseNamespacePath($baseObj);
        $arr3 = BaseObject::parseNamespacePath("\Kph\Objects\BaseObject");

        $cls1 = StrictCls::getShortName();
        $cls2 = BaseObject::getShortName($baseObj);
        $cls3 = BaseObject::getShortName("\PHPUnit\Framework\TestCase");

        $nsp1 = StrictCls::getNamespaceName();
        $nsp2 = BaseObject::getNamespaceName($baseObj);
        $nsp3 = BaseObject::getNamespaceName("\PHPUnit\Framework\TestCase");

        $this->assertEquals(4, count($arr1));
        $this->assertEquals(4, count($arr2));
        $this->assertEquals(3, count($arr3));

        $this->assertEquals('StrictCls', $cls1);
        $this->assertEquals('BaseCls', $cls2);
        $this->assertEquals('TestCase', $cls3);

        $this->assertEquals("Kph\Tests\Objects", $nsp1);
        $this->assertEquals("Kph\Tests\Objects", $nsp2);
        $this->assertEquals("PHPUnit\Framework", $nsp3);
    }


    /**
     * 严格对象测试
     * @throws ReflectionException
     * @throws Throwable
     */
    public function testStrict() {
        $striObj = new StrictCls(['name' => 'zhang3']);
        $ref     = $striObj->getReflectionObject();
        $this->assertTrue($ref instanceof ReflectionClass);

        // 空属性
        try {
            $striObj->get('');
        } catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'empty property') !== false);
        }

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
        } catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined readable property') !== false);
        }

        // 访问属性存在,但getXXX方法私有
        try {
            $no = $striObj->get('no');
        } catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined readable property') !== false);
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
        } catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined writable property') !== false);
        }

        // 设置属性存在,但getXXX方法私有
        try {
            $striObj->set('no', 2);
        } catch (Exception $e) {
            $this->assertTrue(stripos($e->getMessage(), 'Undefined writable property') !== false);
        }

        // 销毁属性
        $key = 'name';
        $this->assertEquals(true, $striObj->isset($key));
        $striObj->unset($key);
        $this->assertEquals(false, $striObj->isset($key));
        $this->assertEquals(false, $striObj->isset('null'));

        // json化
        $json1 = json_encode($striObj);
        $arr1  = json_decode($json1, true);
        $json2 = $striObj->toJson();
        $arr2  = $striObj->toArray();

        $this->assertEquals($json1, $json2);
        $this->assertEqualsCanonicalizing($arr1, $arr2);

    }


    /**
     * 数组对象测试
     */
    public function testArray() {
        $arrObj = new ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertEquals($arrObj->a, 1);

        $arrObj->d = 4;
        $this->assertTrue($arrObj->offsetExists('d'));

        $arrObj->offsetSet('d', 5);
        $this->assertEquals($arrObj->offsetGet('d'), 5);

        $arrObj->set('e', 5);
        $this->assertEquals($arrObj->get('e'), 5);

        //json
        $json1 = json_encode($arrObj);
        $json2 = $arrObj->toJson();
        $this->assertEquals($json1, $json2);

        $count1 = $arrObj->count();
        $seri   = $arrObj->serialize();
        $arrObj->unserialize($seri);
        $count2 = $arrObj->count();
        $this->assertEquals($count1, $count2);

        $this->assertEquals($arrObj->current(), 1);
        $this->assertEquals($arrObj->next(), 2);

        $this->assertEquals($arrObj->key(), 'b');
        $this->assertTrue($arrObj->valid());

        $arrObj->rewind();
        $this->assertEquals($arrObj->key(), 'a');

        $arr = $arrObj->toArray();
        $this->assertEquals($arrObj->count(), count($arr));

        $idx  = $arrObj->search(3);
        $idx2 = $arrObj->indexOf(3);
        $this->assertEquals($idx, $idx2);

        $idx3 = $arrObj->lastIndexOf(5);
        $this->assertEquals($idx3, 'e');

        $keys = $arrObj->keys(5);
        $this->assertEquals($keys->count(), 2);

        $this->assertTrue($arrObj->delete('e'));

        $arrObj->remove(5);
        $arrObj->offsetUnset('c');
        $this->assertFalse($arrObj->exists('e'));
        $this->assertFalse($arrObj->exists('c'));
        $this->assertFalse($arrObj->contains(5));

        $str = $arrObj->join(',');
        $this->assertFalse(empty($str));

        $arrObj->clear();
        $this->assertTrue($arrObj->isEmpty());

        $arrObj->insert(0, 2);
        $arrObj->insert(2, 6);
        $arrObj->append(3);
        $arrObj->prepend(1);

        $sum  = $arrObj->sum();
        $pro  = $arrObj->product();
        $sum2 = $arrObj->reduce(function ($carry, $item) {
            $carry += $item;
            return $carry;
        });
        $this->assertEquals($sum, $pro);
        $this->assertEquals($sum, $sum2);

        $obj2 = $arrObj->slice(0, 2);
        $this->assertEquals($obj2->count(), 2);

        $obj2->pop();
        $obj2->shift();
        $this->assertEquals($obj2->count(), 0);

        $item = $arrObj->rand();
        $this->assertTrue($arrObj->contains($item));

        $arrObj->each(function (&$val, $key) {
            $val = pow($val, $key);
        });
        $this->assertEquals($arrObj->sum(), 12);

        $obj3 = $arrObj->map(function ($val) {
            return $val * 2;
        });
        $this->assertEquals($obj3->sum(), 24);

        $values = $arrObj->values();
        $keys   = $arrObj->keys();
        $this->assertEquals($values->count(), $keys->count());

        $arrObj->prepend(2);
        $arrObj->prepend(3);
        $arrObj->prepend(9);

        $obj4 = $arrObj->unique();
        $this->assertTrue($obj4->count() < $arrObj->count());

        $obj5 = $arrObj->multiple();
        $this->assertEquals($obj5->count(), 2);

        $arrObj->sort();
        $this->assertEquals($arrObj->current(), 1);

        $arrObj->reverse();
        $this->assertEquals($arrObj->current(), 9);

        $arrObj->shuffle();
        $obj6 = $arrObj->chunk(3);
        $this->assertEquals($obj6->count(), 2);

        $obj7 = $obj4->flip();
        $this->assertEquals($obj7->current(), 0);

        $obj8 = $arrObj->filter(function ($val) {
            return $val > 4;
        });
        $this->assertEquals($obj8->count(), 2);

        $arrObj->clear();
        $arrObj->append(['name' => 'zhang3', 'age' => '20',]);
        $arrObj->append(['name' => 'li4', 'age' => '22',]);
        $arrObj->append(['name' => 'zhao5', 'age' => '33',]);
        $arrObj->append(['name' => 'wang6', 'age' => '45',]);
        $names = $arrObj->column('name');
        $this->assertEquals($names->count(), 4);

    }


    public function testGetClassMethods() {
        $res1 = BaseObject::getClassMethods(BaseCls::class);
        $res2 = BaseObject::getClassMethods(BaseCls::class, ReflectionMethod::IS_STATIC);
        $res3 = BaseObject::getClassMethods(BaseCls::class, null, false);
        $dif1 = array_diff($res1, $res2);

        $chk1 = ValidateHelper::isEqualArray($res3, ['time', '__call']);
        $chk2 = ValidateHelper::isEqualArray($dif1, ['time', '__call', '__toString']);
        var_dump('$res3', $res3);
        $this->assertTrue($chk1);
        $this->assertTrue($chk2);

        $res4 = BaseObject::getClassMethods(StrictCls::class);
        $res5 = BaseObject::getClassMethods(StrictCls::class, ReflectionMethod::IS_PROTECTED);
        $res6 = BaseObject::getClassMethods(StrictCls::class, ReflectionMethod::IS_PUBLIC && ReflectionMethod::IS_STATIC, false);
        $dif2 = array_diff($res4, $res5);
        var_dump('$res6', $res6);
        $chk3 = ValidateHelper::isEqualArray($res6, ['world']);
        $this->assertTrue($chk3);
        $this->assertNotEmpty($dif2);

    }


}