<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:10
 * Desc: 严格对象
 */

namespace Kph\Objects;

use Closure;
use Exception;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

class StrictObject extends BaseObject implements JsonSerializable {


    /**
     * 反射对象
     * @var object
     */
    private $__refCls;


    public function __construct($vars = []) {

    }


    /**
     * 获取该类的反射对象
     * @return object|ReflectionClass
     * @throws ReflectionException
     */
    public function getReflectionObject() {
        if(is_null($this->__refCls)) {
            $this->__refCls = new ReflectionClass($this);
        }

        return $this->__refCls;
    }



    public function get(string $name) {
        $methodName = 'get' . ucfirst($name);
        if(isset($this->$name)) {
            return $this->$name;
        }


    }


}