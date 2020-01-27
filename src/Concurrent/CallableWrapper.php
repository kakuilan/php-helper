<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 10:14
 * Desc: 可调用包装器
 */

namespace Kph\Concurrent;

use Exception;

/**
 * Class CallableWrapper
 * @package Kph\Concurrent
 */
class CallableWrapper extends Wrapper {


    /**
     * @return Promise
     * @throws Exception
     */
    public function __invoke() {
        $obj = $this->obj;
        return Promise::all(func_get_args())->then(function ($args) use ($obj) {
            return call_user_func_array($obj, $args);
        });
    }

}