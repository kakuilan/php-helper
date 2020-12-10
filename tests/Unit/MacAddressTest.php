<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/12/9
 * Time: 20:25
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Util\MacAddress;


class MacAddressTest extends TestCase {

    public function testGetAddress() {
        $res0 = MacAddress::getAddress('');
        $res1 = MacAddress::getAddress('eth0');
        $res2 = MacAddress::getAddress('hello');

        $this->assertNotEmpty($res0);
        $this->assertEmpty($res2);
    }

}