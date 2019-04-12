<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/4/3
 * Time: 17:15
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kph\Tests\Objects\BaseCls;

class ObjectsTest extends TestCase {

    public function testBase() {
        $baseObj = new BaseCls();

        $this->assertEquals(strval($baseObj), get_class($baseObj));
        $this->assertEquals($baseObj->getClassShortName(), 'BaseCls');
    }



}