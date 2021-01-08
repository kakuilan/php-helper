<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/6/26
 * Time: 19:11
 * Desc: 包装器类
 */


namespace Kph\Concurrent;

use Kph\Objects\BaseObject;
use ReflectionMethod;
use Exception;
use Throwable;

/**
 * Class Wrapper
 * @package Kph\Concurrent
 */
class Wrapper extends BaseObject {

    protected $obj;

    public function __construct($obj) {
        $this->obj = $obj;
    }


    /**
     * @param $name
     * @param array $arguments
     * @return Future
     * @throws Throwable
     */
    public function __call($name, array $arguments):Future {
        $method = [$this->obj, $name];
        return all($arguments)->then(function($args) use ($method, $name) {
            return co(call_user_func_array($method, $args));
        });
    }


    public function __get($name) {
        return $this->obj->$name ?? null;
    }


    public function __set($name, $value) {
        $this->obj->$name = $value;
    }


    public function __isset($name) {
        return isset($this->obj->$name);
    }


    public function __unset($name) {
        unset($this->obj->$name);
    }


}