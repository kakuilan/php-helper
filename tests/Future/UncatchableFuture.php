<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/3/8
 * Time: 16:01
 * Desc:
 */

namespace Kph\Tests\Future;

use Kph\Exceptions\UncatchableException;


/**
 * Class UncatchableFuture
 * @package Kph\Tests\Future
 */
class UncatchableFuture {


    /**
     * @param callable $resolve
     * @param callable $reject
     * @throws UncatchableException
     */
    public function then(callable $resolve, callable $reject) {
        throw new UncatchableException('Un callable');
    }

}