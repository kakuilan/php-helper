<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/3/8
 * Time: 15:59
 * Desc:
 */

namespace Kph\Tests\Future;


/**
 * Class SuccessFuture
 * @package Kph\Tests\Future
 */
class SuccessFuture {


    /**
     * @param callable $resolve
     * @param callable $reject
     */
    public function then(callable $resolve, callable $reject) {
        $resolve('OK');
    }

}