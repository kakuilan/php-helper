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
        $file = TESTDIR .'data/bom.txt';
        $str = file_get_contents($file);
        $len1 = strlen($str);

        $res = FileHelper::removeBom($str);
        $len2 = strlen($res);

        $this->assertEquals(3, ($len1 - $len2));
    }


    public function testCreateZip() {
        $files = [
            TESTDIR .'tmp/abc/test.log',
            TESTDIR .'../src',
            TESTDIR .'../vendor',
        ];
        $dest = TESTDIR .'tmp/test.zip';

        $res1 = FileHelper::createZip($files, $dest, true);
        $res2 = FileHelper::createZip($files, $dest, false);
        $this->assertTrue($res1);
        $this->assertFalse($res2);
    }


    public function testImg2Base64() {
        $img = TESTDIR .'data/php_elephant.png';
        $str = FileHelper::img2Base64($img);

        $this->assertNotEmpty($str);
        $this->assertGreaterThan(1, strpos($str, 'png'));

        $img = TESTDIR .'data/png.webp';
        $str = FileHelper::img2Base64($img);
        $this->assertGreaterThan(1, strpos($str, 'webp'));
    }



}