<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/6/8
 * Time: 21:32
 * Desc: 基础服务类
 */

namespace Kph\Services;

use Kph\Objects\StrictObject;
use Exception;
use Throwable;

/**
 * Class BaseService
 * @package Kph\Services
 */
class BaseService extends StrictObject {


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
     * BaseService constructor.
     * @param array $vars
     * @throws Throwable
     */
    public function __construct($vars = []) {
        parent::__construct($vars);
    }


    /**
     * 析构函数
     */
    public function __destruct() {

    }


    /**
     * 获取错误代码
     * @return mixed
     */
    public function getErrno() {
        return $this->errno;
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string {
        return strval($this->error);
    }


    /**
     * 设置服务错误信息
     * @param string $error 错误信息
     * @param int|mixed $errno 错误代码
     */
    public function setErrorInfo(string $error = '', $errno = null) {
        $this->error = $error;
        $this->errno = $errno;
    }


    /**
     * 获取服务错误信息
     * @return array
     */
    public function getErrorInfo(): array {
        return ['errno' => $this->errno, 'error' => $this->error,];
    }


}