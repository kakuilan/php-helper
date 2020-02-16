<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/13
 * Time: 10:25
 * Desc:
 */


namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Error;
use Exception;
use ReflectionException;
use Kph\Helpers\ArrayHelper;


class ArrayHelperTest extends TestCase {

    public function testDstrpos() {
        $str = 'hello world. 你好，世界！';
        $arr = ['php', 'Hello', 'today'];

        $res1 = ArrayHelper::dstrpos($str, $arr, false, false);
        $this->assertTrue($res1);

        $res2 = ArrayHelper::dstrpos($str, $arr, true, false);
        $res3 = ArrayHelper::dstrpos($str, $arr, true, true);
        $this->assertEquals('Hello', $res2);
        $this->assertFalse($res3);

        $res4 = ArrayHelper::dstrpos('', $arr);
        $this->assertFalse($res4);
    }


    public function testMultiArraySort() {
        $arr1 = [
            [
                'id' => 9,
                'age' => 19,
                'name' => 'hehe',
                'nick' => '阿斯蒂芬',
            ],
            [
                'id' => 2,
                'age' => 31,
                'name' => 'lizz',
                'nick' => '去玩儿',
            ],
            [
                'id' => 87,
                'age' => 50,
                'name' => 'zhang3',
                'nick' => '谱曲说',
            ],
            [
                'id' => 25,
                'age' => 43,
                'name' => 'wang5',
                'nick' => '阿斯蒂芬',
            ],
            [
                'id' => 24,
                'age' => 63,
                'name' => 'zhao4',
                'nick' => '权威认证',
            ],
        ];

        $arr2 = array_merge($arr1, ['hello']);
        $arr3 = array_merge($arr1, [
            'age' => 44,
            'name' => 'asdf',
            'nick' => '主线程v',
        ]);

        $res1 = ArrayHelper::multiArraySort($arr1, 'id', SORT_ASC);
        $res2 = ArrayHelper::multiArraySort($arr2, 'id', SORT_ASC);
        $res3 = ArrayHelper::multiArraySort($arr3, 'id', SORT_ASC);

        $id1 = $res1[0]['id'] ?? 0;
        $id2 = $res1[1]['id'] ?? 0;
        $this->assertNotEmpty($res1);
        $this->assertGreaterThan($id1, $id2);

        $this->assertEmpty($res2);
        $this->assertEmpty($res3);
    }


    public function testMultiArrayUnique() {
        $arr = [
            'aa' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id' => 2,
                'age' => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id' => 87,
                'age' => 50,
                'name' => 'zhang3',
            ],
        ];

        $res1 = ArrayHelper::multiArrayUnique([]);
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::multiArrayUnique($arr, false);
        $this->assertEquals(3, count($res2));

        $res3 = ArrayHelper::multiArrayUnique($arr, true);
        $keys = array_keys($res3);
        $this->assertTrue(in_array('aa', $keys));
    }


    public function testMultiArrayValues() {
        $arr = [
            'aa' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id' => 2,
                'age' => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id' => 87,
                'age' => 50,
                'name' => 'zhang3',
            ],
        ];

        $res = ArrayHelper::multiArrayValues($arr);
        $this->assertEquals(12, count($res));
    }


    public function testArraySort() {
        $arr = [
            'aa' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id' => 2,
                'age' => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id' => 87,
                'age' => 50,
                'name' => 'zhang3',
            ],
        ];

        $res1 = ArrayHelper::arraySort([], 'id');
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::arraySort($arr, 'comp');
        $this->assertEmpty($res2);

        $res3 = ArrayHelper::arraySort($arr, 'id', 'desc', false);
        $first = current($res3);
        $this->assertEquals(87, $first['id']);

        $res4 = ArrayHelper::arraySort($arr, 'id', 'asc', true);
        $keys = array_keys($res4);
        $first = current($res4);
        $this->assertEquals(2, $first['id']);
        $this->assertTrue(in_array('aa', $keys));
    }


    public function testArrayMapRecursive() {
        $arr = [-3, 0, 4, 7, 87];
        $fn = function (int $val):int {
            return 2 * $val;
        };

        $res = ArrayHelper::arrayMapRecursive($arr, $fn);
        $this->assertEquals(count($arr), count($res));
    }


    public function testObject2Array() {
        $childs = [];
        for ($i=1;$i<5;$i++) {
            $chi = new \stdClass();
            $chi->id = $i;
            $chi->type = 'child';
            $chi->name = 'boy-' . strval($i);
            $chi->childs = [];

            array_push($childs, $chi);
        }

        $par = new \stdClass();
        $par->id = 0;
        $par->type = 'parent';
        $par->name = 'hello';
        $par->childs = $childs;

        $res1 = ArrayHelper::object2Array(new \stdClass());
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::object2Array($chi);
        $this->assertEquals(4, count($res2));

        $res3 = ArrayHelper::object2Array($par);
        $this->assertEquals(4, count($res3['childs']));
    }


    public function testArrayToObject() {
        $arr = [
            'aa' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
                'child' => [],
            ],
            'bb' => [
                'id' => 2,
                'age' => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id' => 9,
                'age' => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id' => 87,
                'age' => 50,
                'name' => 'zhang3',
            ],
        ];

        $res1 = ArrayHelper::array2Object([]);
        $this->assertTrue(is_object($res1));

        $res2 = ArrayHelper::array2Object($arr);
        $this->assertTrue(is_object($res2->aa->child));
    }





}