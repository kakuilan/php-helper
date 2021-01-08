<?php
/**
 * Copyright (c) 2019 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/4/3
 * Time: 17:03
 * Desc: 基本对象
 */

namespace Kph\Objects;

use Kph\Helpers\DirectoryHelper;
use Kph\Helpers\StringHelper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

/**
 * Class BaseObject
 * @package Kph\Objects
 */
class BaseObject {

    /**
     * 最终子类实例化
     * @var object
     */
    protected static $_self;


    /**
     * 最终父类实例化
     * @var object
     */
    protected static $_final;


    /**
     * 打印时转字符串
     * @return string
     */
    public function __toString(): string {
        return get_class($this);
    }


    /**
     * 格式化命名空间
     * @param string $str
     * @return string
     */
    public static function formatNamespace(string $str): string {
        $str = DirectoryHelper::formatDir($str);
        $str = str_replace('/', '\\', $str);
        return substr($str, 0, -1);
    }


    /**
     * 获取类名
     * @param mixed $var 对象或带命名空间的类名
     * @return string
     */
    public static function getClass($var = null): string {
        if (is_object($var)) {
            $cls = get_class($var);
        } elseif ($var == '' || is_null($var)) {
            $cls = static::class;
        } else {
            $cls = strval($var);
            $cls = self::formatNamespace($cls);
        }

        return $cls;
    }


    /**
     * 解析命名空间路径
     * @param mixed $var 对象或带命名空间的类名/函数名
     * @return array
     */
    public static function parseNamespacePath($var = null): array {
        $cls = self::getClass($var);
        $res = StringHelper::multiExplode($cls, '\\', '/');

        return array_filter($res);
    }


    /**
     * 获取短名(不包含命名空间)
     * @param mixed $var 对象或带命名空间的类名/函数名
     * @return string
     */
    public static function getShortName($var = null): string {
        $arr = self::parseNamespacePath($var);
        return end($arr);
    }


    /**
     * 获取命名空间
     * @param mixed $var 对象或带命名空间的类名/函数名
     * @return string
     */
    public static function getNamespaceName($var = null): string {
        $arr = self::parseNamespacePath($var);
        array_pop($arr);
        return implode('\\', $arr);
    }


    /**
     * 获取类的方法列表
     * @param mixed $var 对象或带命名空间的类名
     * @param int|null $filter 过滤器,如 ReflectionMethod::IS_STATIC | ReflectionMethod::IS_FINAL
     * @param bool $includeParent 是否包括父类的方法
     * @return array
     * @throws ReflectionException
     */
    public static function getClassMethods($var = null, int $filter = null, bool $includeParent = true): array {
        $res     = [];
        $name    = self::getClass($var);
        $class   = new ReflectionClass($name);
        $methods = is_null($filter) ? $class->getMethods() : $class->getMethods($filter);
        if (!empty($methods)) {
            foreach ($methods as $methodObj) {
                array_push($res, $methodObj->name);
            }

            //不包括父类的方法
            if (!$includeParent && $parentClass = get_parent_class($name)) {
                $parentMethods = get_class_methods($parentClass);
                if (!empty($parentMethods)) {
                    $res = array_diff($res, $parentMethods);
                }
            }
        }

        return $res;
    }


    /**
     * 实例化并返回[调用此方法的类,静态绑定]
     * 不可重写
     * @return object
     */
    final public static function getSelfInstance(): object {
        if (is_null(static::$_self) || !is_object(static::$_self) || !(static::$_self instanceof static)) {
            //静态延迟绑定
            static::$_self = new static();
        }

        return static::$_self;
    }


    /**
     * 是否存在当前[调用]类实例化
     * 不可重写
     * @return bool
     */
    final public static function hasSelfInstance(): bool {
        return isset(static::$_self);
    }


    /**
     * 销毁当前[调用]类实例化
     * 不可重写
     */
    final public static function destroySelfInstance(): void {
        static::$_self = null;
    }


    /**
     * 实例化并返回[最终父类]
     * 可重写,返回所重写的类
     * 当前返回BaseObject
     * @return BaseObject
     */
    public static function getFinalInstance(): BaseObject {
        if (is_null(self::$_final) || !is_object(self::$_final) || !(self::$_final instanceof self)) {
            self::$_final = new self();
        }

        return self::$_final;
    }


    /**
     * 是否存在最终类实例化
     * 可重写
     * @return bool
     */
    public static function hasFinalInstance(): bool {
        return isset(self::$_final);
    }


    /**
     * 销毁最终类实例化
     * 可重写
     */
    public static function destroyFinalInstance(): void {
        self::$_final = null;
    }


}