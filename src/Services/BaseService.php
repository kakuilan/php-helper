<?php
/**
 * Created by PhpStorm.
 * User: kakuilan@163.com
 * Date: 2019/6/8
 * Time: 21:32
 * Desc: 基础服务类
 */

namespace Kph\Services;

use Kph\Objects\BaseObject;

class BaseService extends BaseObject {


    /**
     * 错误码
     * @var int|string
     */
    public $errno;


    /**
     * 错误消息
     * @var string
     */
    public $error;


    /**
     * 供静态绑定
     * @var object
     */
    protected static $instance;


    /**
     * 供最终子类绑定
     * @var object
     */
    protected static $_instance;


    /**
     * 构造函数
     * BaseService constructor.
     * @param array $vars
     */
    public function __construct($vars=[]) {
        parent::__construct($vars);
    }


    /**
     * 析构函数
     */
    public function __destruct() {

    }


    /**
     * 实例化并返回[父类]
     * @param array $vars
     * @return mixed
     */
    public static function instance(array $vars = []) {
        if(is_null(self::$instance) || !is_object(self::$instance) || !(self::$instance instanceof self)) {
            self::$instance = new self($vars);
        }

        return self::$instance;
    }


    /**
     * 实例化并返回[静态绑定,供(当前)子类调用]
     * @param array $vars
     *
     * @return mixed
     */
    public static function getInstance(array $vars = []) {
        if(is_null(static::$_instance) || !is_object(static::$_instance) || !(static::$_instance instanceof static)) {
            //静态延迟绑定
            static::$_instance = new static($vars);
        }

        return static::$_instance;
    }


    /**
     * 销毁实例化对象
     */
    public static function destroy() {
        self::$instance = null;
        static::$_instance = null;
    }


    /**
     * 获取错误代码
     * @return mixed
     */
    public function errno() {
        return $this->errno;
    }


    /**
     * 获取错误信息
     * @return mixed
     */
    public function error() {
        return $this->error;
    }


    /**
     * 设置服务错误
     * @param string $error 错误信息
     * @param string $errno 错误代码
     */
    public function setError($error='', $errno='') {
        $this->error = $error;
        $this->errno = $errno;
    }


    /**
     * 获取服务错误
     * @return array
     */
    public function getError() {
        return [
            'errno' => $this->errno,
            'error' => $this->error,
        ];
    }


}