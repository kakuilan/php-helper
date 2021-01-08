<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/6/26
 * Time: 19:15
 * Desc: 参考 https://github.com/hprose/hprose-php/wiki/03-Promise-%E5%BC%82%E6%AD%A5%E7%BC%96%E7%A8%8B
 */


namespace Kph\Concurrent;

use Exception;
use Throwable;

/**
 * Class Promise
 * @package Kph\Concurrent
 */
class Promise extends Future {


    /**
     * Promise constructor.
     * @param null $executor
     * @throws Throwable
     */
    public function __construct($executor = null) {
        parent::__construct();

        if (is_callable($executor)) {
            $self = $this;
            call_user_func($executor, function ($value = null) use ($self) {
                $self->resolve($value);
            }, function ($reason = null) use ($self) {
                $self->reject($reason);
            });
        }
    }

}