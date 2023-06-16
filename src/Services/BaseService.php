<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/6/8
 * Time: 21:32
 * Desc: 基础服务类
 */

namespace Kph\Services;

use Kph\Consts;
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
     * 处理结果
     * @var mixed
     */
    public $result = null;


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
     * @return int
     */
    public function getErrno() {
        return intval($this->errno);
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string {
        $res = strval($this->error);
        if ($this->errno && empty($res)) {
            $res = Consts::UNKNOWN;
        }

        return $res;
    }


    /**
     * 设置服务错误信息
     * @param string $error 错误信息
     * @param int|mixed $errno 错误代码
     */
    public function setErrorInfo(string $error = '', $errno = null) {
        if ($error) {
            $this->error = $error;
        }

        if ($errno) {
            $this->errno = intval($errno);
        }
    }


    /**
     * 获取服务错误信息
     * @return array
     */
    public function getErrorInfo(): array {
        return [
            'errno' => $this->getErrno(),
            'error' => $this->getError(),
        ];
    }


    /**
     * 设置结果
     * @param mixed $arr
     * @return void
     */
    public function setResult($arr): void {
        $this->result = $arr;
    }


    /**
     * 获取结果
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }

}