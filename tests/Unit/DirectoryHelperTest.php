<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/22
 * Time: 19:37
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\DirectoryHelper;


class DirectoryHelperTest extends TestCase {


    public function testMkdirDeep() {
        $dir = TESTDIR . 'tmp/test/2020/0223';
        $res = DirectoryHelper::mkdirDeep($dir);
        $this->assertTrue($res);

        $res = DirectoryHelper::mkdirDeep('');
        $this->assertFalse($res);

        $res = DirectoryHelper::mkdirDeep($dir);
        $this->assertTrue($res);

        DirectoryHelper::mkdirDeep('/root/hello');
    }


    public function testGetFileTree() {
        $all   = DirectoryHelper::getFileTree(TESTDIR);
        $dirs  = DirectoryHelper::getFileTree(TESTDIR, 'dir');
        $files = DirectoryHelper::getFileTree(TESTDIR, 'file');
        $this->assertEquals(count($all), count($dirs) + count($files));

        DirectoryHelper::getFileTree(TESTDIR, 'file', true);
    }


    public function testGetDirSize() {
        $res = DirectoryHelper::getDirSize(TESTDIR);
        $this->assertGreaterThan(1, $res);

        $res = DirectoryHelper::getDirSize('');
        $this->assertEquals(0, $res);
    }


    public function testCopyDirEmptyDirDelDir() {
        $backupDir1 = TESTDIR . 'tmp/backup/1';
        $backupDir2 = TESTDIR . 'tmp/backup/2';
        DirectoryHelper::chmodBatch($backupDir1, 766, 766);
        DirectoryHelper::chmodBatch($backupDir2, 766, 766);

        $fromDir = dirname(TESTDIR) . '/src';
        $res1    = DirectoryHelper::copyDir($fromDir, $backupDir1);
        $res2    = DirectoryHelper::copyDir($fromDir, $backupDir1, true);
        $res3    = DirectoryHelper::copyDir($fromDir, $backupDir2);
        $res4    = DirectoryHelper::copyDir($fromDir, $backupDir2, true);
        $res5    = DirectoryHelper::copyDir($fromDir, $backupDir2, false);

        $this->assertTrue($res1);
        $this->assertTrue($res2);
        $this->assertTrue($res3);
        $this->assertTrue($res4);
        $this->assertTrue($res5);

        DirectoryHelper::copyDir($fromDir, '/root/tmp');
        $files1 = DirectoryHelper::getFileTree($backupDir1);
        $files2 = DirectoryHelper::getFileTree($backupDir2);
        $this->assertGreaterThan(1, $files1);
        $this->assertGreaterThan(1, $files2);

        DirectoryHelper::chmodBatch('', 777, 777);
        DirectoryHelper::chmodBatch('/root', 777, 777);
        DirectoryHelper::chmodBatch($backupDir1, 777, 777);

        //emptyDir
        $res5 = DirectoryHelper::emptyDir('');
        $res6 = DirectoryHelper::emptyDir($backupDir1);
        $this->assertFalse($res5);
        $this->assertTrue($res6);
        $this->assertTrue(is_dir($backupDir1));

        //delDir
        $res7 = DirectoryHelper::delDir('');
        $res8 = DirectoryHelper::delDir($backupDir2);
        $this->assertFalse($res7);
        $this->assertTrue($res8);
        $this->assertFalse(is_dir($backupDir2));
    }


    public function testFormatDir() {
        $res1 = DirectoryHelper::formatDir('');
        $res2 = DirectoryHelper::formatDir('/usr///tmp\\\123/\abc\hello\/world\\%how$');

        $this->assertEmpty($res1);
        $this->assertEquals('/usr/tmp/123/abc/hello/world/how/', $res2);
    }


}