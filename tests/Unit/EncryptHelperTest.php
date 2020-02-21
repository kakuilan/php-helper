<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/21
 * Time: 13:17
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\EncryptHelper;


class EncryptHelperTest extends TestCase {

    public function testAuthcode() {
        $origin = 'hello world!';
        $key = '123456';

        $enres = EncryptHelper::authcode($origin, $key, true, 3600);
        $deres = EncryptHelper::authcode($enres[0], $key, false);
        $this->assertEquals($origin, $deres[0]);
        $this->assertEquals($enres[1], $deres[1]);

        $res1 = EncryptHelper::authcode('', '', true);
        $res2 = EncryptHelper::authcode('', '', false);
        $this->assertEquals('', $res1[0]);
        $this->assertEquals('', $res2[0]);
    }


}