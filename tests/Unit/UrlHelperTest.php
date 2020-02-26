<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/26
 * Time: 10:57
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\UrlHelper;


class UrlHelperTest extends TestCase {

    public function testCnUrlencodeDecode() {
        $url  = "http://www.abc3210.com/s?wd=博客&name=张 三&age=20&qu='quote'";
        $res1 = UrlHelper::cnUrlencode($url);
        $res2 = UrlHelper::cnUrldecode($res1);

        $this->assertEquals($url, $res2);
    }


    public function testBuildUriParams() {
        $arr = [
            'name' => 'li4',
            'age'  => 28,
            'has'  => [
                0          => 'apple',
                1          => 'watermelon',
                2          => 'banana',
                'favorite' => 'litchi',
            ],
            'from' => 'xiguan',
            'to'   => 'baoan',
        ];

        $res1 = UrlHelper::buildUriParams($arr);
        $res2 = UrlHelper::buildUriParams($arr, ['from', 'to']);
        $res3 = UrlHelper::buildUriParams($arr, ['from', 'to'], ['futian', 'dogu']);

        $this->assertEquals('?', substr($res1, 0, 1));
        $this->assertFalse(stripos($res2, 'from'));
        $this->assertTrue(stripos($res3, 'from') !== false);
    }


    public function testFormatUrl() {
        $url = 'www.test.loc//abc\\hello/\kit\/name=zang&age=11';
        $res = UrlHelper::formatUrl($url);

        $this->assertEquals('http://www.test.loc/abc/hello/kit/name=zang&age=11', $res);
    }


    public function testCheckUrlExists() {
        $res1 = UrlHelper::checkUrlExists('');
        $res2 = UrlHelper::checkUrlExists('hello world');
        $res3 = UrlHelper::checkUrlExists('https://www.baidu.com/');

        $this->assertFalse($res1);
        $this->assertFalse($res2);
        $this->assertTrue($res3);
    }


    public function testUrl2Link() {
        $res1 = UrlHelper::url2Link('');
        $res2 = UrlHelper::url2Link('http://google.com');
        $res3 = UrlHelper::url2Link('ftp://192.168.1.2/abc.pdf', ['ftp']);
        $res4 = UrlHelper::url2Link('test@qq.com', ['mail']);

        $this->assertEmpty($res1);
        $this->assertTrue(stripos($res2, 'href') !== false);
        $this->assertTrue(stripos($res3, 'href') !== false);
        $this->assertTrue(stripos($res4, 'href') !== false);
    }


}