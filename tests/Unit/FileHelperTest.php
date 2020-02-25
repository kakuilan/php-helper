<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/25
 * Time: 14:09
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\FileHelper;


class FileHelperTest extends TestCase {


    public function testGetFileExt() {
        $ext1 = FileHelper::getFileExt(__FILE__);
        $ext2 = FileHelper::getFileExt('http://www.abc.com/hello/world.jpg?width=100');
        $this->assertEquals('php', $ext1);
        $this->assertEquals('jpg', $ext2);
    }


    public function testWriteFile() {
        $file = TESTDIR .'tmp/abc/test.log';
        $res = FileHelper::writeFile($file, 'hello world');
        $this->assertTrue($res);
        $this->assertTrue(file_exists($file));
    }


    public function testRemoveBom() {
        $file = TESTDIR .'tmp/bom.txt';
        $str = file_get_contents($file);
        $len1 = strlen($str);

        $res = FileHelper::removeBom($str);
        $len2 = strlen($res);

        $this->assertEquals(3, ($len1 - $len2));
    }


}