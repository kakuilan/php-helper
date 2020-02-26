<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/26
 * Time: 13:26
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\StringHelper;
use Kph\Helpers\ValidateHelper;


class StringHelperTest extends TestCase {


    public function testMd5Short() {
        $res1 = StringHelper::md5Short('');
        $res2 = StringHelper::md5Short('hello');

        $this->assertEquals(16, strlen($res1));
        $this->assertEquals(16, strlen($res2));
    }


    public function testCutStr() {
        $res1 = StringHelper::cutStr('hello你好，world,世界！', 6);
        $res2 = StringHelper::cutStr('hello你好，world,世界！', 9, 0, '…');

        $this->assertNotEmpty($res1);
        $this->assertTrue(mb_strpos($res2, '你好') !== false);
    }


    public function testLength() {
        $str  = 'hello ,你好，world.世界！&amp;';
        $res1 = StringHelper::length($str, false);
        $res2 = StringHelper::length($str, true);

        $this->assertLessThan($res1, $res2);
    }


    public function testRandSimple() {
        $res1 = StringHelper::randSimple();
        $res2 = StringHelper::randSimple(10, true);

        $this->assertEquals(6, strlen($res1));
        $this->assertEquals(10, strlen($res2));
    }


    public function testRandNumber() {
        $res1 = StringHelper::randNumber();
        $res2 = StringHelper::randNumber(10);

        $this->assertEquals(6, strlen($res1));
        $this->assertEquals(10, strlen($res2));
    }


    public function testRandString() {
        $res1 = StringHelper::randString();
        $res2 = StringHelper::randString(10, 1);
        $res3 = StringHelper::randString(10, 2);
        $res4 = StringHelper::randString(10, 3);
        $res5 = StringHelper::randString(10, 4);
        $res6 = StringHelper::randString(10, 5);
        $res7 = StringHelper::randString(10, 0, '!@#$%^&*');

        $this->assertEquals(6, strlen($res1));
        $this->assertTrue(ValidateHelper::isLetter($res2));
        $this->assertTrue(is_numeric($res3));
        $this->assertTrue(ValidateHelper::isUpperLetter($res4));
        $this->assertTrue(ValidateHelper::isLowerLetter($res5));
        $this->assertTrue(ValidateHelper::isChinese($res6));
        $this->assertEquals(10, strlen($res7));
    }


    public function testFixHtml() {
        $str1 = '这是一段被截断的html文本<a href="#"';
        $str2 = '这是一段被截断的html文本<a href="#">';
        $res1 = StringHelper::fixHtml($str1);
        $res2 = StringHelper::fixHtml($str2);
        $res3 = StringHelper::fixHtml('hello');

        $this->assertFalse(stripos($res1, 'a'));
        $this->assertEquals(2, substr_count($res2, 'a'));
        $this->assertEquals('hello', $res3);
    }


    public function testSBCxDBC() {
        $str  = 'HelloWorld';
        $res1 = StringHelper::DBC2SBC($str);
        $res2 = StringHelper::SBC2DBC($res1);

        $this->assertEquals(30, strlen($res1));
        $this->assertEquals($str, $res2);
    }


    public function testGetClosestWord() {
        $item = 'hello PHP';
        $arr  = ["Hello,goper", "hehe,python!", $item, "haha,java", "I`m php."];
        $str  = 'hello,php';

        $res = StringHelper::getClosestWord($str, $arr);
        $this->assertEquals($item, $res[0]);
    }


    public function testEscapeUnescape() {
        $str = 'Some \' problematic \\ chars " ... ?wd=博客&name=张 三&age=20&qu=\'quote\'';

        $res1 = StringHelper::escape($str);
        $res2 = StringHelper::unescape($res1);

        $this->assertEquals($str, $res2);
    }


    public function testGetFirstLetter() {
        $tests = [
            ['', ''],
            ['-~!@#$', ''],
            ['hello', 'H'],
            ['安徽', 'A'],
            ['北京', 'B'],
            ['长沙', 'C'],
            ['东莞', 'D'],
            ['鄂州', 'E'],
            ['法师', 'F'],
            ['公共', 'G'],
            ['很好', 'H'],
            ['简介', 'J'],
            ['开封', 'K'],
            ['拉链', 'L'],
            ['美工', 'M'],
            ['南宁', 'N'],
            ['藕片', 'O'],
            ['匹配', 'P'],
            ['请求', 'Q'],
            ['仍然', 'R'],
            ['赛事', 'S'],
            ['天天', 'T'],
            ['外网', 'W'],
            ['信息', 'X'],
            ['应用', 'Y'],
            ['正则', 'Z'],
        ];

        foreach ($tests as $test) {
            $expected = StringHelper::getFirstLetter($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testMatchImages() {
        $html = <<<EOF
        <h1>标题</h1>
        <p>段落
        <img src="/images/logo.png">
        </p>
        <p>
        <a><img src="http://test.com/static/img/abc.jpt"></a>
        </p>
EOF;

        $res1 = StringHelper::matchImages('');
        $res2 = StringHelper::matchImages($html);

        $this->assertEmpty($res1);
        $this->assertEquals(2, count($res2));
    }


}