<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/5/11
 * Time: 18:48
 * Desc: 可数组化接口
 */

namespace Kph\Interfaces;


/**
 * Interface Arrayable
 * @package Kph\Interfaces
 */
interface Arrayable {

    /**
     * Get the instance as an array
     * @return array
     */
    public function toArray(): array;

}