<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/3/8
 * Time: 16:01
 * Desc:
 */

namespace Kph\Tests\Future;

use Exception;


/**
 * Class FailFuture
 * @package Kph\Tests\Future
 */
class FailFuture {


    /**
     * @param callable $resolve
     * @param callable $reject
     */
    public function then(callable $resolve, callable $reject) {
        $reject('fail');
    }

}