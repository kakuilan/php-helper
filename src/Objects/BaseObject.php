<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:03
 * Desc: 基本对象
 */

namespace Kph\Objects;


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
     * 获取类的短名(不包含命名空间)
     * @return string
     */
    public function getClassShortName(): string {
        $arr = explode('\\', get_class($this));
        return end($arr);
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