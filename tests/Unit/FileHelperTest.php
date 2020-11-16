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
        $file = TESTDIR . 'tmp/abc/test.log';
        $res  = FileHelper::writeFile($file, 'hello world');
        $this->assertTrue($res);
        $this->assertTrue(file_exists($file));
    }


    public function testRemoveBom() {
        $file = TESTDIR . 'data/bom.txt';
        $str  = file_get_contents($file);
        $len1 = strlen($str);

        $res  = FileHelper::removeBom($str);
        $len2 = strlen($res);

        $this->assertEquals(3, ($len1 - $len2));
    }


    public function testCreateZip() {
        $files = [
            TESTDIR . 'tmp/abc/test.log',
            TESTDIR . '../src',
            TESTDIR . '../vendor',
        ];
        $dest  = TESTDIR . 'tmp/test.zip';
        $dest2 = TESTDIR . 'tmp/hello/test.zip';

        $res1 = FileHelper::createZip($files, $dest, true);
        $res2 = FileHelper::createZip($files, $dest, false);
        $res3 = FileHelper::createZip([], $dest2, false);
        $res4 = FileHelper::createZip($files, $dest2, true);
        $this->assertTrue($res1);
        $this->assertFalse($res2);
        $this->assertFalse($res3);
        $this->assertFalse($res4);
        FileHelper::createZip($files, '/root/tmp/test.zip', true);
    }


    public function testImg2Base64() {
        $img = TESTDIR . 'data/php_elephant.png';
        $str = FileHelper::img2Base64($img);
        $this->assertNotEmpty($str);
        $this->assertGreaterThan(1, strpos($str, 'png'));

        $img = TESTDIR . 'data/png.webp';
        $str = FileHelper::img2Base64($img);
        $this->assertGreaterThan(1, strpos($str, 'webp'));

        $img = TESTDIR . 'data/banana.gif';
        $str = FileHelper::img2Base64($img);
        $this->assertGreaterThan(1, strpos($str, 'gif'));

        $img = TESTDIR . 'data/green.jpg';
        $str = FileHelper::img2Base64($img);
        $this->assertGreaterThan(1, strpos($str, 'jpg'));

        $str = FileHelper::img2Base64('');
        $this->assertEmpty($str);
    }


    public function testGetAllMimes() {
        $res = FileHelper::getAllMimes();
        $this->assertGreaterThan(1, count($res));
    }


    public function testGetFileMime() {
        $img1 = TESTDIR . 'data/png.webp';
        $img2 = TESTDIR . 'data/php-logo.svg';
        $mim1 = FileHelper::getFileMime($img1);
        $mim2 = FileHelper::getFileMime($img2);

        $this->assertNotEmpty($mim1);
        $this->assertNotEmpty($mim2);
    }


    public function testReadInArray() {
        $file = TESTDIR . 'data/bom.txt';
        $arr  = FileHelper::readInArray($file);
        $this->assertGreaterThan(1, count($arr));

        $arr = FileHelper::readInArray('/tmp/hello/1234');
        $this->assertEmpty($arr);
    }


    public function testFormatPath() {
        $res1 = FileHelper::formatPath('');
        $res2 = FileHelper::formatPath('/usr|///tmp:\\\123/\abc<|\hello>\/%world?\\how$\\are\@#test.png');

        $this->assertEmpty($res1);
        $this->assertEquals('/usr/tmp/123/abc/hello/%world/how$/are/@#test.png', $res2);
    }


    public function testGetAbsPath() {
        $path1 = 'ArrayHelperTest.php';
        $path2 = './Unit/DateHelperTest.php';
        $path3 = '../../docs/changelog.md';
        $path4 = '../../../.gitignore';

        $res0 = FileHelper::getAbsPath('');
        $res1 = FileHelper::getAbsPath($path1, __DIR__);
        $res2 = FileHelper::getAbsPath($path2, TESTDIR);
        $res3 = FileHelper::getAbsPath($path3, __DIR__);
        $res4 = FileHelper::getAbsPath($path4);

        $this->assertEmpty($res0);
        $this->assertTrue(file_exists($res1));
        $this->assertTrue(file_exists($res2));
        $this->assertTrue(file_exists($res3));
        $this->assertFalse(file_exists($res4));
    }


    public function testGetRelativePath() {
        $f1 = '/var/www/php/ci/a.php';
        $f2 = '/usr/local/php/log/test.log';
        $f3 = '/var/www/php/zf/b.php';
        $f4 = '/var/www/img/a.php';
        $f5 = '/var/www/api/img/b.php';

        $res1 = FileHelper::getRelativePath($f1, '');
        $res2 = FileHelper::getRelativePath($f1, $f2);
        $res3 = FileHelper::getRelativePath($f1, $f3);
        $res4 = FileHelper::getRelativePath($f4, $f5);
        $res5 = FileHelper::getRelativePath($f5, $f4);

        $this->assertEquals($res1, $f1);
        $this->assertEquals($res2, $f1);
        $this->assertEquals($res3, '../../ci/a.php');
        $this->assertEquals($res4, '../../../img/a.php');
        $this->assertEquals($res5, '../../api/img/b.php');
    }


}