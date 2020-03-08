<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/3/8
 * Time: 13:43
 * Desc: 可抛错接口
 */

namespace Kph\Interfaces;

use Throwable as BaseThrowable;


/**
 * Interface Throwable
 * @package Kph\Interfaces
 */
interface Throwable extends BaseThrowable {

    /**
     * 获取异常概要
     * @return string
     */
    public function getSummary(): string;

}