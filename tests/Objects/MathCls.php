<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/29
 * Time: 18:53
 * Desc:
 */

namespace Kph\Tests\Objects;

use Kph\Objects\BaseObject;

class MathCls extends BaseObject {

    public function add($a, $b) {
        return $a + $b;
    }

    public function sub($a, $b) {
        return $a - $b;
    }

    public function mul($a, $b) {
        return $a * $b;
    }

    public function div($a, $b) {
        return $a / $b;
    }

}