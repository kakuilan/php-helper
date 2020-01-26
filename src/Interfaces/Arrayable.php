<?php
/**
 * Created by PhpStorm.
 * User: kakuilan@163.com
 * Date: 2019/5/11
 * Time: 18:48
 * Desc: 可数组化接口
 */

namespace Kph\Interfaces;

interface Arrayable {

    /**
     * Get the instance as an array
     * @return array
     */
    public function toArray(): array;

}