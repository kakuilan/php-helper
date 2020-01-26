<?php
/**
 * Created by PhpStorm.
 * User: kakuilan@163.com
 * Date: 2019/5/11
 * Time: 18:50
 * Desc: 可json化接口
 */

namespace Kph\Interfaces;

interface Jsonable {


    /**
     * Convert the object to its JSON representation
     * @param int $options
     * @param int $depth
     * @return string
     */
    public function toJson(int $options=0, int $depth = 512):string ;

}