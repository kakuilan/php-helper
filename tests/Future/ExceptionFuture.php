<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/3/8
 * Time: 16:02
 * Desc:
 */

namespace Kph\Tests\Future;

use Exception;


/**
 * Class ExceptionFuture
 * @package Kph\Tests\Future
 */
class ExceptionFuture {


    /**
     * @param callable $resolve
     * @param callable $reject
     * @throws Exception
     */
    public function then(callable $resolve, callable $reject) {
        throw new Exception('has error');
    }

}