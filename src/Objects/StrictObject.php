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

use Error;
use Exception;
use JsonSerializable;
use Kph\Interfaces\Arrayable;
use Kph\Interfaces\Jsonable;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class StrictObject
 * @package Kph\Objects
 */
class StrictObject extends BaseObject implements JsonSerializable, Arrayable, Jsonable {


    /**
     * 反射对象
     * @var object
     */
    private $__refCls;


    /**
     * 类json字段(属性)
     * @var array
     */
    private $jsonFields = null;


    /**
     * StrictObject constructor.
     * @param array $vars
     * @throws Exception
     */
    public function __construct(array $vars = []) {
        foreach ($vars as $field => $value) {
            $this->set($field, $value);
        }
    }


    /**
     * 获取该类的反射对象
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public function getReflectionObject() {
        if (is_null($this->__refCls)) {
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
        if (is_null($name) || trim($name) === '') {
            throw new Exception('empty property: ' . static::class . '::');
        }
    }


    /**
     * 获取属性值或调用获取方法,如 get(name) => getName()
     * @param string $name
     * @return mixed|void
     * @throws Exception
     */
    final public function get(string $name) {
        $this->__checkEmptyProperty($name);

        // 获取public、protected属性
        if (property_exists($this, $name)) {
            try {
                return $this->$name;
            } catch (Error $e) {
            }
        }

        // 对private属性,调用getXXX方法
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            try {
                return $this->$methodName();
            } catch (Error $e) {
            }
        }

        return $this->__undefinedGetWarn($name);
    }


    /**
     * 设置属性值或调用设置方法,如 set(name,val) => setName(val)
     * @param string $name
     * @param mixed $value
     * @return bool|void
     * @throws Exception
     */
    final public function set(string $name, $value = null) {
        $this->__checkEmptyProperty($name);

        // 设置public、protected属性
        if (property_exists($this, $name)) {
            try {
                $this->$name = $value;
                return true;
            } catch (Error $e) {
            }
        }

        // 对private属性,调用setXXX方法
        $methodName = 'set' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            try {
                $this->$methodName($value);
                return true;
            } catch (Error $e) {
            }
        }

        return $this->__undefinedSetWarn($name);
    }


    /**
     * 属性是否存在(包括NULL值)
     * @param $name
     * @return bool
     */
    public function isset($name): bool {
        $res = isset($this->$name);
        if (!$res) {
            try {
                $res = is_null($this->$name);
            } catch (Error $e) {
            } catch (Exception $e) {
            }
        }

        return $res;
    }


    /**
     * 销毁属性
     * @param $name
     */
    public function unset($name): void {
        unset($this->$name, $this->jsonFields[$name]);
    }


    /**
     * 获取可json字段
     * @return array
     * @throws ReflectionException
     */
    protected function getJsonFields(): array {
        if (is_null($this->jsonFields)) {
            $this->jsonFields = [];
            $ref              = $this->getReflectionObject();
            array_map(function (ReflectionProperty $fieldObj) {
                array_push($this->jsonFields, $fieldObj->getName());
            }, array_filter($ref->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC), function (ReflectionProperty $field) {
                return !$field->isStatic();
            }));
        }

        return $this->jsonFields;
    }


    /**
     * json序列化(仅public、protected的属性)
     * @return array
     * @throws ReflectionException
     */
    public function jsonSerialize(): array {
        $arr    = [];
        $fields = $this->getJsonFields();
        if (!empty($fields)) {
            array_map(function ($field) use (&$arr) {
                $arr[$field] = $this->{$field};
            }, array_filter($fields, function (string $field) {
                //过滤已销毁的属性
                return $this->isset($field);
            }));
        }
        unset($fields);

        return $arr;
    }


    /**
     * 转为数组
     * @return array
     * @throws ReflectionException
     */
    public function toArray(): array {
        return $this->jsonSerialize();
    }


    /**
     * 转为json串
     * @param int $options
     * @param int $depth
     * @return string
     * @throws ReflectionException
     */
    public function toJson(int $options = 0, int $depth = 512): string {
        return json_encode($this->jsonSerialize(), $options, $depth);
    }


}