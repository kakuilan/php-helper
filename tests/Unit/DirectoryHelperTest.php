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
    }


    public function testGetFileTree() {
        $all   = DirectoryHelper::getFileTree(TESTDIR);
        $dirs  = DirectoryHelper::getFileTree(TESTDIR, 'dir');
        $files = DirectoryHelper::getFileTree(TESTDIR, 'file');
        $this->assertEquals(count($all), count($dirs) + count($files));

        DirectoryHelper::getFileTree(TESTDIR, 'file', true);
    }

}