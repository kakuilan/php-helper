<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/26
 * Time: 10:57
 * Desc:
 */

namespace Kph\Tests\Unit;

use Error;
use Exception;
use Kph\Helpers\StringHelper;
use Kph\Helpers\UrlHelper;
use Kph\Helpers\ValidateHelper;
use PHPUnit\Framework\TestCase;


class UrlHelperTest extends TestCase {

    public function testCnUrlencodeDecode() {
        $url  = "http://www.abc3210.com/s?wd=博客&name=张 三&age=20&qu='quote'®";
        $res1 = UrlHelper::cnUrlencode($url);
        $res2 = UrlHelper::cnUrldecode($res1);

        $str  = '©℗';
        $str2 = StringHelper::escape($str);
        $res3 = UrlHelper::cnUrldecode($str2);

        $this->assertEquals($url, $res2);
        $this->assertEquals('&#169;&#8471;', $res3);
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


    public function testGetDomainUrlUri() {
        $server = OsHelperTest::$server;
        $url    = 'http://www.test.loc/index.php?name=hello&age=20&from=world';
        $res1   = UrlHelper::getDomain($url, false, $server);
        $res2   = UrlHelper::getDomain($url, true, $server);

        $this->assertEquals('www.test.loc', $res1);
        $this->assertEquals('test.loc', $res2);

        $res3 = UrlHelper::getUrl($server);
        $this->assertEquals($url, $res3);

        $res4 = UrlHelper::getUri($server);
        unset($server['REQUEST_URI']);
        $res5 = UrlHelper::getUri($server);
        $this->assertEquals($res4, $res5);

        $res6 = UrlHelper::getDomain('', true, $server);
        $this->assertEquals('test.loc', $res6);

        $res7 = UrlHelper::getUrl();
        $this->assertFalse(ValidateHelper::isUrl($res7));

        $res8 = UrlHelper::getUri();
        $this->assertNotEmpty($res8);

        $res9 = UrlHelper::getDomain('');
        $this->assertEmpty($res9);
    }


    public function testGetSiteUrl() {
        $server = OsHelperTest::$server;
        $str    = 'hello world!';
        $url1   = 'http://www.test.loc/index.php?name=hello&age=20&from=world';
        $url2   = 'rpc.test.com:8899/hello';

        $res1 = UrlHelper::getSiteUrl($str);
        $this->assertEmpty($res1);

        $res2 = UrlHelper::getSiteUrl('', $server);
        $this->assertNotEmpty($res2);

        $res3 = UrlHelper::getSiteUrl($url1);
        $this->assertNotEmpty($res3);

        $res4 = UrlHelper::getSiteUrl($url2);
        $this->assertNotEmpty($res4);
    }


}