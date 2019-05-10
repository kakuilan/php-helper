<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:10
 * Desc: 严格对象,可json序列化
 * Warn: 属性或方法须采用驼峰命名规则
 */

namespace Kph\Objects;

use Closure;
use Error;
use Exception;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class StrictObject extends BaseObject implements JsonSerializable {


    /**
     * 反射对象
     * @var object
     */
    private $__refCls;


    /**
     * 类json字段(属性)
     * @var array
     */
    private $jsonFields = [];


    /**
     * StrictObject constructor.
     * @param array $vars
     */
    public function __construct($vars = []) {
        foreach ($vars as $field => $value) {
            $this->set($field, $value);
        }
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


    /**
     * 获取未定义时警告
     * @param string $name 名称
     * @throws Exception
     */
    protected function __undefinedGetWarn(string $name) {
        throw new Exception('Undefined readable property: ' . static::class . '::' . $name);
    }


    /**
     * 设置未定义时警告
     * @param string $name
     * @throws Exception
     */
    protected function __undefinedSetWarn(string $name) {
        throw new Exception('Undefined writable property: ' . static::class . '::' . $name);
    }


    /**
     * 检查是否空属性
     * @param string $name
     * @throws Exception
     */
    protected function __checkEmptyProperty(string $name) {
        if(is_null($name) || $name==='') {
            throw new Exception('empty property: ' . static::class . '::');
        }
    }


    /**
     * 获取属性值或调用获取方法,如 get(name) => getName()
     * @param string $name
     * @throws Exception
     */
    final public function get(string $name) {
        $this->__checkEmptyProperty($name);

        if(property_exists($this, $name)) {
            try {
                return $this->$name;
            }catch (Exception $e) {
            }
        }

        $methodName = 'get' . ucfirst($name);
        if(method_exists($this, $methodName)) {
            try {
                return $this->$methodName();
            }catch (Error $e) {
            }catch (Exception $e) {
            }
        }

        return $this->__undefinedGetWarn($name);
    }


    /**
     * Get value with getter via magic method
     * @param $name
     * @throws Exception
     */
    public function __get($name) {
        return $this->get($name);
    }


    /**
     * 设置属性值或调用设置方法,如 set(name,val) => setName(val)
     * @param string $name
     * @param null $value
     * @return bool|void
     * @throws Exception
     */
    final public function set(string $name, $value=null) {
        $this->__checkEmptyProperty($name);

        if(property_exists($this, $name)) {
            try {
                $tmp = $this->$name;
                $this->$name = $value;
                return true;
            }catch (Exception $e) {
            }
        }

        $methodName = 'set' . ucfirst($name);
        if(method_exists($this, $methodName)) {
            try {
                $this->$methodName($value);
                return true;
            }catch (Error $e) {
            }catch (Exception $e) {
            }
        }

        return $this->__undefinedSetWarn($name);
    }


    /**
     * Set value with setter via magic method
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }


    public function __isset($name){
        return isset($this->$name);
    }


    public function __unset($name){
        unset($this->$name);
    }


    /**
     * 获取可json字段
     * @return array
     */
    protected function getJsonFields() {
        return $this->jsonFields;
    }


    /**
     * json序列化
     * @return array|mixed
     * @throws ReflectionException
     */
    public function jsonSerialize() {
        $fields = $this->getJsonFields();
        if (count($fields) === 0) {
            $ref = $this->getReflectionObject();
            $json = array_map(function (ReflectionProperty $fieldObj) {
                $field = $fieldObj->getName();
                array_push($this->jsonFields, $field);
                return $this->{$field};
            }, array_filter($ref->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC), function (ReflectionProperty $field) {
                return !$field->isStatic();
            }));
        } else {
            $json = array_map(function ($field) {
                if ($field instanceof Closure) {
                    return $field();
                }
                return $this->{$field};
            }, $fields);
        }

        return $json;
    }

}