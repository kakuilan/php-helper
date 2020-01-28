<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2019/6/26
 * Time: 19:11
 * Desc: 包装器类
 */


namespace Kph\Concurrent;

use Kph\Objects\BaseObject;
use ReflectionMethod;
use Exception;

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
     * @throws Exception
     */
    public function __call($name, array $arguments):Future {
        $method = [$this->obj, $name];
        return all($arguments)->then(function($args) use ($method, $name) {
            if (class_exists("\\Generator", false)) {
                return co(call_user_func_array($method, $args));
            }
            return call_user_func_array($method, $args);
        });
    }


    public function __get($name) {
        return $this->obj->$name;
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