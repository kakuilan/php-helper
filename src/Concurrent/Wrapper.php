<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2019/6/26
 * Time: 19:11
 * Desc: 包装器类
 */


namespace Kph\Concurrent;

use Kph\Objects\BaseObject;
use ReflectionMethod;

class Wrapper extends BaseObject {

    protected $obj;

    public function __construct($obj) {
        $this->obj = $obj;
    }

    public function __call($name, array $arguments) {
        $method = array($this->obj, $name);
        return Promise::all($arguments)->then(function($args) use ($method, $name) {
            if (class_exists("\\Generator")) {
                $m = new ReflectionMethod($this->obj, $name);
                if ($m->isGenerator()) {
                    return Promise::co(call_user_func_array($method, $args));
                }
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