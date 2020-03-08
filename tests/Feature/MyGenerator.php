<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/28
 * Time: 17:14
 * Desc:
 */

namespace Kph\Tests\Feature;

use Faker\Factory;
use Generator;

class MyGenerator {

    /**
     * 随机名称的生成器
     * @return Generator
     */
    public static function randName() {
        $faker = Factory::create();
        yield $faker->name;
    }


    /**
     * 随机地址的生成器
     * @return Generator
     */
    public static function randAddr() {
        $faker = Factory::create();
        yield $faker->address;
    }


    /**
     * 随机数字的生成器
     * @return Generator
     */
    public static function randNum() {
        $num = mt_rand(1, 9999);
        yield $num;
    }


    /**
     * 数字的生成器
     * @return Generator
     */
    public static function num() {
        for ($i = 1; $i <= 99; $i++) {
            //注意变量$i的值在不同的yield之间是保持传递的。
            yield $i;
        }
    }


    /**
     * @param int $a
     * @param int $b
     * @param mixed $callback
     */
    public static function asyncSum(int $a, int $b, $callback=null) {
        $total = $a + $b;
        if(!empty($callback) && is_callable($callback)) {
            $total = $callback($total);
        }

        return $total;
    }


    /**
     * @param int $a
     * @param int $b
     * @param callable $callback
     * @return mixed
     */
    public static function asyncSumNone(int $a, int $b, callable $callback) {
        return $callback();
    }


    /**
     * @param int $a
     * @param int $b
     * @param callable $callback
     * @return mixed
     */
    public static function asyncSumDoubly(int $a, int $b, callable $callback) {
        $total = $a + $b;
        return $callback($a, $b, $total);
    }


    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public static function asyncSumError($a, $b, $c, $d):void {
        return;
    }


}