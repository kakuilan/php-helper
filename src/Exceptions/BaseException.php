<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/3/8
 * Time: 13:49
 * Desc: 基础异常
 */

namespace Kph\Exceptions;

use Exception;
use Kph\Interfaces\Throwable;


/**
 * Class BaseException
 * @package Kph\Exceptions
 */
class BaseException extends Exception implements Throwable {


    /**
     * 获取异常概要
     * @return string
     */
    public function getSummary(): string {
        $msg = $this->getMessage() . ' ##code:' . $this->getCode() . ' ##file:' . $this->getFile() . ' ##line:' . $this->getLine();
        return $msg;
    }

}

