<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 10:14
 * Desc: 可调用包装器
 */

namespace Kph\Concurrent;

use Exception;
use Throwable;

/**
 * Class CallableWrapper
 * @package Kph\Concurrent
 */
class CallableWrapper extends Wrapper {

    /**
     * 当尝试以调用函数的方式调用一个对象时，__invoke() 方法会被自动调用
     * @return Future
     * @throws Throwable
     */
    public function __invoke() {
        $obj = $this->obj;
        return all(func_get_args())->then(function($args) use ($obj) {
            return co(call_user_func_array($obj, $args));
        });
    }

}