<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 14:06
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kph\Concurrent\Promise;
use Faker\Factory;
use Exception;
use Generator;

class PromiseTest extends TestCase {


    /**
     * 随机名称的生成器
     * @return Generator
     */
    public static function randName() {
        $total = 50;
        $faker = Factory::create();
        for ($i=0;$i<$total;$i++) {
            $name = $faker->name;
            //echo "name:{$name}\r\n";
            yield;
        }
    }


    /**
     * 随机地址的生成器
     * @return Generator
     */
    public static function randAddr() {
        $total = 50;
        $faker = Factory::create();
        for ($i=0;$i<$total;$i++) {
            $addr = $faker->address;
            //echo "addr:{$addr}\r\n";
            yield;
        }
    }


    /**
     * 数字的生成器
     * @return Generator
     */
    public static function num() {
        $total = 50;
        for ($i=0;$i<$total;$i++) {
            $num = $i;
            //echo "num:{$num}\r\n";
            yield;
        }
    }


    /**
     * 测试是否promise
     */
    public function testIsPromise() {
        $chk = Promise::isPromise($this);
        $this->assertFalse($chk);

        try {
            $obj = Promise::value(1);
        }catch (Exception $e) {

        }

        $chk = Promise::isPromise($obj);
        $this->assertTrue($chk);
    }


    public function testPromiseCo() {
        $res = Promise::co(function() {
            yield self::randName();
            yield self::randAddr();
            yield self::num();
        });


    }

}