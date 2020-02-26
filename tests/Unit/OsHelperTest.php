<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/25
 * Time: 16:48
 * Desc:
 */

namespace Kph\Tests\Unit;

use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\OsHelper;


class OsHelperTest extends TestCase {


    public static $server = [
        'DOCUMENT_ROOT'                  => '/var/www',
        'REMOTE_ADDR'                    => '172.17.0.1',
        'REMOTE_PORT'                    => '51186',
        'SERVER_SOFTWARE'                => 'PHP 7.4.3 Development Server',
        'SERVER_PROTOCOL'                => 'HTTP/1.1',
        'SERVER_NAME'                    => '0.0.0.0',
        'SERVER_PORT'                    => '8000',
        'REQUEST_URI'                    => '/index.php?name=hello&age=20&from=world',
        'REQUEST_METHOD'                 => 'GET',
        'SCRIPT_NAME'                    => '/index.php',
        'SCRIPT_FILENAME'                => '/var/www/index.php',
        'PHP_SELF'                       => '/index.php',
        'QUERY_STRING'                   => 'name=hello&age=20&from=world',
        'HTTP_X_REAL_IP'                 => '192.168.56.1',
        'HTTP_X_FORWARDED_FOR'           => '192.168.56.1',
        'HTTP_X_REAL_PORT'               => '80',
        'HTTP_X_FORWARDED_PROTO'         => 'http',
        'HTTP_HOST'                      => 'www.test.loc',
        'HTTP_USER_AGENT'                => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0',
        'HTTP_ACCEPT'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'HTTP_ACCEPT_LANGUAGE'           => 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
        'HTTP_ACCEPT_ENCODING'           => 'gzip, deflate',
        'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
        'REQUEST_TIME_FLOAT'             => 1582623381.699998,
        'REQUEST_TIME'                   => 1582623381,
    ];


    public function testIsWindowsLinux() {
        $res1 = OsHelper::isWindows();
        $res2 = OsHelper::isLinux();

        $this->assertFalse($res1);
        $this->assertTrue($res2);
    }


    public function testIsPortOpen() {
        $res1 = OsHelper::isPortOpen('localhost', 80);
        $res2 = OsHelper::isPortOpen('baidu.com', 80);

        $this->assertFalse($res1);
        $this->assertTrue($res2);
    }


    public function testIsWritable() {
        $res1 = OsHelper::isWritable(TESTDIR . 'tmp');
        $res2 = OsHelper::isWritable('/root/tmp/hehe');

        $this->assertTrue($res1);
        $this->assertFalse($res2);
    }


    public function testGetBrowser() {
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
        $res   = OsHelper::getBrowser($agent);

        $this->assertGreaterThan(1, stripos($res['name'], 'Chrome'));
        $this->assertNotEmpty($res['platform']);
    }


    public function testGetClientOS() {
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
        $res   = OsHelper::getClientOS($agent);

        $this->assertEquals('Windows', $res);
    }


    public function testGetClientIp() {
        $res = OsHelper::getClientIp(self::$server);
        $this->assertEquals('192.168.56.1', $res);
    }


    public function testGetServerIP() {
        $res = OsHelper::getServerIP(self::$server);
        $this->assertNotEmpty($res);
        $this->assertNotEquals('0.0.0.0', $res);
    }


    public function testGetDomain() {
        $url = 'http://www.test.loc/index.php?name=hello&age=20&from=world';
        $res1 = OsHelper::getDomain($url, false, self::$server);
        $res2 = OsHelper::getDomain($url, true, self::$server);

        $this->assertEquals('www.test.loc', $res1);
        $this->assertEquals('test.loc', $res2);
    }



}