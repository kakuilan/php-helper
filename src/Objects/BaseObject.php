<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:03
 * Desc: 基本对象
 */

namespace Kph\Objects;

class BaseObject {

    public function __toString(){
        return get_class($this);
    }


    /**
     * 获取类的短名(不包含命名空间)
     * @return string
     */
    public function getClassShortName() {
        $arr = explode('\\', get_class($this));
        return end($arr);
    }


}