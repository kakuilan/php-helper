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
use Error;
use Exception;
use Kph\Helpers\ArrayHelper;


class ArrayHelperTest extends TestCase {


    public function testMultiArraySort() {
        $arr1 = [
            [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hehe',
                'nick' => '阿斯蒂芬',
            ],
            [
                'id'   => 2,
                'age'  => 31,
                'name' => 'lizz',
                'nick' => '去玩儿',
            ],
            [
                'id'   => 87,
                'age'  => 50,
                'name' => 'zhang3',
                'nick' => '谱曲说',
            ],
            [
                'id'   => 25,
                'age'  => 43,
                'name' => 'wang5',
                'nick' => '阿斯蒂芬',
            ],
            [
                'id'   => 24,
                'age'  => 63,
                'name' => 'zhao4',
                'nick' => '权威认证',
            ],
        ];

        $arr2 = array_merge($arr1, ['hello']);
        $arr3 = array_merge($arr1, [
            'age'  => 44,
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
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id'   => 2,
                'age'  => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id'   => 87,
                'age'  => 50,
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
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id'   => 2,
                'age'  => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id'   => 87,
                'age'  => 50,
                'name' => 'zhang3',
            ],
        ];

        $res = ArrayHelper::multiArrayValues($arr);
        $this->assertEquals(12, count($res));
    }


    public function testArraySort() {
        $arr = [
            'aa' => [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'bb' => [
                'id'   => 2,
                'age'  => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id'   => 87,
                'age'  => 50,
                'name' => 'zhang3',
            ],
        ];

        $res1 = ArrayHelper::sortByField([], 'id');
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::sortByField($arr, 'comp');
        $this->assertEmpty($res2);

        $res3  = ArrayHelper::sortByField($arr, 'id', 'desc', false);
        $first = current($res3);
        $this->assertEquals(87, $first['id']);

        $res4  = ArrayHelper::sortByField($arr, 'id', 'asc', true);
        $keys  = array_keys($res4);
        $first = current($res4);
        $this->assertEquals(2, $first['id']);
        $this->assertTrue(in_array('aa', $keys));
    }


    public function testmapRecursive() {
        $arr = [-3, 0, 4, 7, 87];
        $fn  = function (int $val): int {
            return 2 * $val;
        };

        $res = ArrayHelper::mapRecursive($arr, $fn);
        $this->assertEquals(count($arr), count($res));
    }


    public function testObject2Array() {
        $childs = [];
        for ($i = 1; $i < 5; $i++) {
            $chi         = new \stdClass();
            $chi->id     = $i;
            $chi->type   = 'child';
            $chi->name   = 'boy-' . strval($i);
            $chi->childs = [];

            array_push($childs, $chi);
        }

        $par         = new \stdClass();
        $par->id     = 0;
        $par->type   = 'parent';
        $par->name   = 'hello';
        $par->childs = $childs;

        $res1 = ArrayHelper::object2Array(new \stdClass());
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::object2Array($chi);
        $this->assertEquals(4, count($res2));

        $res3 = ArrayHelper::object2Array($par);
        $this->assertEquals(4, count($res3['childs']));

        $res4 = ArrayHelper::object2Array(1);
        $this->assertEquals(1, count($res4));
    }


    public function testArrayToObject() {
        $arr = [
            'aa' => [
                'id'    => 9,
                'age'   => 19,
                'name'  => 'hello',
                'child' => [],
            ],
            'bb' => [
                'id'   => 2,
                'age'  => 31,
                'name' => 'lizz',
            ],
            'cc' => [
                'id'   => 9,
                'age'  => 19,
                'name' => 'hello',
            ],
            'dd' => [
                'id'   => 87,
                'age'  => 50,
                'name' => 'zhang3',
            ],
        ];

        $res1 = ArrayHelper::array2Object([]);
        $this->assertTrue(is_object($res1));

        $res2 = ArrayHelper::array2Object($arr);
        $this->assertTrue(is_object($res2->aa->child));
    }


    public function testCutItems() {
        $arr = [
            0   => 'aa',
            3   => 'ww',
            'a' => 4,
            'd' => 56.78,
            'e' => true,
            '8' => 'hello',
            9   => false,
        ];

        [$res1, $res2, $res3, $res4] = ArrayHelper::cutItems($arr, 3, 'a', 'd', 'p');
        $this->assertEquals('ww', $res1);
        $this->assertEquals(4, $res2);
        $this->assertEquals(56.78, $res3);
        $this->assertNull($res4);
        $this->assertEquals(4, count($arr));
    }


    public function testcombinationFull() {
        $arr = ['a', 'b', 'c', 'd'];

        $res1 = ArrayHelper::combinationFull($arr, '-', true);
        $this->assertEquals(15, count($res1));

        $res2 = ArrayHelper::combinationFull($arr, '-', false);
        $this->assertEquals(64, count($res2));

        $res3 = ArrayHelper::combinationFull([], '-');
        $this->assertEmpty($res3);

        $res4 = ArrayHelper::combinationFull(['a'], '-');
        $this->assertEquals(1, count($res4));

        $res5 = ArrayHelper::combinationAll([], '-');
        $this->assertEmpty($res5);
    }


    public function testSearchItemSearchMutil() {
        $arr = [
            [
                'id'     => 9,
                'gender' => 1,
                'age'    => 19,
                'name'   => 'hehe',
                'nick'   => '阿斯蒂芬',
            ],
            [
                'id'     => 2,
                'gender' => 0,
                'age'    => 31,
                'name'   => 'lizz',
                'nick'   => '去玩儿',
            ],
            [
                'id'     => 87,
                'gender' => 1,
                'age'    => 19,
                'name'   => 'zhang3',
                'nick'   => '谱曲说',
            ],
            [
                'id'     => 25,
                'gender' => 0,
                'age'    => 43,
                'name'   => 'wang5',
                'nick'   => '阿斯蒂芬',
            ],
            [
                'id'     => 24,
                'gender' => 1,
                'age'    => 63,
                'name'   => 'zhao4',
                'nick'   => '权威认证',
            ],
        ];

        $tmp = [];
        $res = ArrayHelper::searchItem($tmp, ['id' => 99]);
        $this->assertFalse($res);
        $res = ArrayHelper::searchItem($arr, ['id' => 99]);
        $this->assertFalse($res);
        $res = ArrayHelper::searchMutil($tmp, ['id' => 99]);
        $this->assertEmpty($res);

        $res1 = ArrayHelper::searchItem($arr, ['id' => 87, 'name' => true]);
        $this->assertTrue(in_array($res1, $arr));

        $res2 = ArrayHelper::searchMutil($arr, ['gender' => 1, 'age' => 19, 'name' => true]);
        $this->assertGreaterThanOrEqual(2, count($res2));

        $newArr = $arr;
        $res3   = ArrayHelper::searchItem($newArr, ['id' => 87], true);
        $this->assertNotEmpty($res3);
        $this->assertFalse(in_array($res3, $newArr));

        $newArr = $arr;
        $len    = count($newArr);
        $res4   = ArrayHelper::searchMutil($newArr, ['gender' => 1, 'age' => 19, 'name' => true], true);
        $this->assertEquals($len, count($res4) + count($newArr));
    }


    public function testSortByMultiFields() {
        $arr = [
            [
                'id'     => 9,
                'gender' => 1,
                'age'    => 19,
                'name'   => 'hehe',
                'nick'   => '阿斯蒂芬',
            ],
            [
                'id'     => 2,
                'gender' => 0,
                'age'    => 31,
                'name'   => 'lizz',
                'nick'   => '去玩儿',
            ],
            [
                'id'     => 87,
                'gender' => 1,
                'age'    => 19,
                'name'   => 'zhang3',
                'nick'   => '谱曲说',
            ],
            [
                'id'     => 25,
                'gender' => 0,
                'age'    => 43,
                'name'   => 'wang5',
                'nick'   => '阿斯蒂芬',
            ],
            [
                'id'     => 24,
                'gender' => 1,
                'age'    => 63,
                'name'   => 'zhao4',
                'nick'   => '权威认证',
            ],
        ];

        $res1 = ArrayHelper::sortByMultiFields([], ['id', SORT_ASC]);
        $this->assertEmpty($res1);

        $res2 = ArrayHelper::sortByMultiFields($arr);
        $this->assertEquals(json_encode($arr), json_encode($res2));

        $res3   = ArrayHelper::sortByMultiFields($arr, ['gender'], ['age', SORT_ASC], ['id', SORT_DESC]);
        $first  = current($res3);
        $second = $res3[1];
        $this->assertEquals(87, $first['id']);
        $this->assertEquals(9, $second['id']);

        $res4 = ArrayHelper::sortByMultiFields($arr, ['gender'], ['age', SORT_ASC], ['type', SORT_DESC]);
        $this->assertEmpty($res4);
    }


    public function testSwapItem() {
        $arr = [
            1   => 1,
            'a' => 'aa',
            'b' => 'bb',
            'c' => 'cc',
            'd' => 'dd',
            3   => 3,
        ];

        $ret1 = ArrayHelper::swapItem($arr, 1, 3);
        $this->assertTrue($ret1);
        $this->assertEquals(3, $arr[1]);
        $this->assertEquals(1, $arr[3]);

        $ret2 = ArrayHelper::swapItem($arr, 'a', 'c');
        $this->assertTrue($ret1);
        $this->assertEquals('cc', $arr['a']);
        $this->assertEquals('aa', $arr['c']);

        $ret3 = ArrayHelper::swapItem($arr, 'a', 't');
        $this->assertFalse($ret3);
    }


    public function testArrDotKey() {
        $arr1 = [1, 2, 3];
        $arr2 = [];
        $arr3 = [
            'aa' => 1,
            'bb' => [],
            'cc' => 'test',
        ];

        //setDotKey
        ArrayHelper::setDotKey($arr1, '', []);
        $this->assertEmpty($arr1);

        ArrayHelper::setDotKey($arr2, '2', 'hello');
        ArrayHelper::setDotKey($arr2, 5, 'world');
        $this->assertEquals(2, count($arr2));

        $name = 'zhang3';
        $age  = 22;
        ArrayHelper::setDotKey($arr3, 'row.usr.name', $name);
        ArrayHelper::setDotKey($arr3, 'row.usr.age', $age);
        ArrayHelper::setDotKey($arr3, 'bb.k1', '33');
        ArrayHelper::setDotKey($arr3, 'cc.k2', '44');

        //getDotKey
        $val1 = ArrayHelper::getDotKey($arr2);
        $val2 = ArrayHelper::getDotKey($arr2, 5);
        $val3 = ArrayHelper::getDotKey($arr3, 'row.usr.name');
        $val4 = ArrayHelper::getDotKey($arr3, 'row.usr.age');
        $val5 = ArrayHelper::getDotKey($arr3, 'row.usr.addr');
        $this->assertEquals(2, count($val1));
        $this->assertEquals('world', $val2);
        $this->assertEquals($name, $val3);
        $this->assertEquals($age, $val4);
        $this->assertEmpty($val5);

        //hasDotKey
        $chk1 = ArrayHelper::hasDotKey($arr2);
        $chk2 = ArrayHelper::hasDotKey($arr2, '2');
        $chk3 = ArrayHelper::hasDotKey($arr3, 'row..usr');
        $chk4 = ArrayHelper::hasDotKey($arr3, 'row.adm');
        $chk5 = ArrayHelper::hasDotKey($arr3, 'row.usr.age');

        $this->assertFalse($chk1);
        $this->assertTrue($chk2);
        $this->assertFalse($chk3);
        $this->assertFalse($chk4);
        $this->assertTrue($chk5);

    }


    public function testCompareSchema() {
        $sub1 = [
            'a' => '1',
            'b' => '2',
            'c' => '3',
        ];
        $sub2 = [
            'b' => '6',
            'a' => '5',
            'c' => '7',
        ];
        $sub3 = [
            'c' => '8',
            'b' => '9',
            'a' => '10',
            'd' => '11',
        ];
        $sub4 = ['hh', 'ee', 'll'];
        $arr1 = [
            'aa'    => '11',
            'bb'    => '12',
            'cc'    => '13',
            'sub'   => $sub1,
            'child' => range(1, 9),
        ];
        $arr2 = [
            'bb'    => '15',
            'aa'    => '14',
            'cc'    => '16',
            'sub'   => $sub2,
            'child' => $sub4,
        ];
        $arr3 = [
            'bb'    => '15',
            'aa'    => '14',
            'cc'    => '16',
            'sub'   => $sub3,
            'child' => [],
        ];
        $arr4 = [
            'bb'    => '15',
            'aa'    => '14',
            'cc'    => '16',
            'sub'   => $sub3,
            'dd'    => [],
            'child' => null,
        ];
        $arr5 = [
            'bb'    => '15',
            'aa'    => '14',
            'cc'    => '16',
            'sub'   => $sub2,
            'child' => $sub3,
        ];
        $arr6 = [
            'bb'    => '15',
            'aa'    => '14',
            'cc'    => '16',
            'sub'   => $sub2,
            'child' => 'hello world',
        ];

        $tests = [
            [$arr1, $arr2, false, true],
            [$arr1, $arr2, true, true],

            [$arr1, $arr3, false, true],
            [$arr1, $arr3, true, false],

            [$arr1, $arr4, false, false],
            [$arr1, $arr4, true, false],

            [$arr2, $arr5, false, true],
            [$arr2, $arr5, true, false],

            [$arr2, $arr6, false, true],
            [$arr2, $arr6, true, false],
        ];
        foreach ($tests as $test) {
            $expected = ArrayHelper::compareSchema($test[0], $test[1], $test[2]);
            $this->assertEquals($test[3], $expected);
        }
    }


    public function testRegularSort() {
        $arr0 = range(1, 9);
        shuffle($arr0);
        $sub0 = [
            "name"         => "Sally",
            "nick_name"    => "sal",
            "availability" => 1,
            "weight"       => 65.4,
            "is_fav"       => false,
            "addr"         => "home",
        ];
        $sub1 = [
            '-1' => 23,
            '8'  => 33,
            '2'  => [],
            '9'  => 'asdf',
            '19' => 'wera',
            '4'  => true,
            '10' => false,
            '0'  => 3.14,
            '-8' => 3.14,
            '5'  => 2.718,
        ];
        $arr1 = [5, 3, 7, 23, 6, 86];
        $arr2 = [
            'B'     => 'great', 'A' => 'good',
            'b'     => 'great', 'a' => 'good',
            'last'  => $arr0,
            'sub'   => $sub0,
            'child' => $sub1,
        ];

        ArrayHelper::regularSort($arr1);
        ArrayHelper::regularSort($arr2);

        $keys1 = implode('', array_keys($arr1));
        $vals1 = implode('', array_values($arr1));
        $this->assertEquals('012345', $keys1);
        $this->assertEquals('35672386', $vals1);

        $keys2 = implode('', array_keys($arr2['child']));
        $vals2 = implode('', array_values($arr2['last']));
        $this->assertEquals('0123456789', $keys2);
        $this->assertEquals('123456789', $vals2);

        $vals3 = implode('', array_values($arr2['sub']));
        $this->assertEquals('home1Sallysal65.4', $vals3);
    }


}