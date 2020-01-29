<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/28
 * Time: 17:14
 * Desc:
 */

namespace Kph\Tests\Objects;

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
     * @return int
     */
    public static function randNum() {
        return mt_rand(1, 99);
    }


    /**
     * @param int $a
     * @param int $b
     * @param callable $callback
     */
    public static function asyncSum(int $a, int $b, callable $callback) {
        $total = $a + $b;
        return $callback($total);
    }




}