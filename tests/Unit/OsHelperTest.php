<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/25
 * Time: 16:48
 * Desc:
 */

namespace Kph\Tests\Unit;

use Error;
use Exception;
use Kph\Consts;
use Kph\Helpers\OsHelper;
use Kph\Helpers\ValidateHelper;
use PHPUnit\Framework\TestCase;


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


    public function testGetOS() {
        $res = OsHelper::getOS();
        $this->assertNotEmpty($res);
    }


    public function testIsWindowsLinuxMac() {
        $res1 = OsHelper::isWindows();
        $res2 = OsHelper::isLinux();
        $res3 = OsHelper::isMac();

        $this->assertTrue($res1 || $res2);
        $this->assertFalse($res3);
    }


    public function testGetPhpPath() {
        $res = OsHelper::getPhpPath();
        $this->assertNotEmpty($res);
    }


    public function testIsPortOpen() {
        $res1 = OsHelper::isPortOpen('localhost', 8899);
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

        $agents = ValidateHelperTest::$userAgents;
        foreach ($agents as $agent) {
            $res = OsHelper::getBrowser($agent);
            $this->assertNotEmpty($res['name']);
        }

        $res = OsHelper::getBrowser();
        $this->assertEquals(Consts::UNKNOWN, $res['name']);
    }


    public function testGetClientOS() {
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36';
        $res1  = OsHelper::getClientOS($agent);
        $res2  = OsHelper::getClientOS('');
        $this->assertEquals('Windows', $res1);
        $this->assertEquals(Consts::UNKNOWN, $res2);

        $agents = ValidateHelperTest::$userAgents;
        foreach ($agents as $agent) {
            $res = OsHelper::getClientOS($agent);
            $this->assertNotEmpty($res);
        }
    }


    public function testGetClientIp() {
        $server = self::$server;
        $res    = OsHelper::getClientIp($server);
        $this->assertEquals('192.168.56.1', $res);

        $server['HTTP_X_FORWARDED_FOR'] = '220.181.38.148';
        $res                            = OsHelper::getClientIp($server);
        $this->assertEquals('220.181.38.148', $res);

        unset($server['HTTP_X_FORWARDED_FOR']);
        $res = OsHelper::getClientIp($server);
        $this->assertEquals('172.17.0.1', $res);

        $res = OsHelper::getClientIp();
        $this->assertEquals('0.0.0.0', $res);
    }


    public function testGetServerIP() {
        $server = self::$server;
        $res    = OsHelper::getServerIP($server);
        $this->assertNotEmpty($res);
        $this->assertNotEquals('0.0.0.0', $res);

        $res = OsHelper::getServerIP();
        $this->assertNotEmpty($res);
        $this->assertNotEquals('0.0.0.0', $res);

        putenv("SERVER_ADDR='192.168.1.1'");
        $res = OsHelper::getServerIP([]);
        $this->assertNotEquals('192.168.1.1', $res);
    }


    public function testIp2UnsignedInt() {
        $ip1 = '172.17.0.1';
        $ip2 = '192.168.56.1';
        $ip3 = 'hello';
        $ip4 = '200.117.248.17';

        $res1 = OsHelper::ip2UnsignedInt($ip1);
        $res2 = OsHelper::ip2UnsignedInt($ip2);
        $res3 = OsHelper::ip2UnsignedInt($ip3);
        $res4 = OsHelper::ip2UnsignedInt($ip4);
        $res5 = OsHelper::ip2UnsignedInt('');

        $this->assertGreaterThan(1, $res1);
        $this->assertGreaterThan(1, $res2);
        $this->assertEquals(0, $res3);
        $this->assertGreaterThan(1, $res4);
        $this->assertEquals(0, $res5);
    }


    public function testGetRemoteImageSize() {
        $url = 'https://www.baidu.com/img/bd_logo1.png';

        $res1 = OsHelper::getRemoteImageSize($url . '?a=1', 'hello', false, 5, 256);
        $res2 = OsHelper::getRemoteImageSize($url . '?a=2', 'curl', true, 5, 256);
        $res3 = OsHelper::getRemoteImageSize('http://test.loc/img/hello.jpg');

        $this->assertNotEmpty($res1);
        $this->assertEquals($res1['width'], $res2['width']);
        $this->assertEquals($res1['height'], $res2['height']);
        $this->assertEquals(0, $res1['size']);
        $this->assertGreaterThan(1, $res2['size']);
        $this->assertEmpty($res3);

        OsHelper::getRemoteImageSize('https://raw.githubusercontent.com/kakuilan/kgo/master/testdata/gopher10th-large.jpg', 'curl', true, 1, 24);
    }


    public function testCurlDownload() {
        $des1 = $backupDir1 = TESTDIR . 'tmp/download.txt';
        $des2 = $backupDir1 = TESTDIR . 'tmp/hello/download.txt';

        $res1 = OsHelper::curlDownload('http://test.loc/hello', '', [], false);
        $res2 = OsHelper::curlDownload('https://www.baidu.com/', '', ['connect_timeout' => 5, 'timeout' => 5], true);
        $res3 = OsHelper::curlDownload('hello world');

        $res4 = OsHelper::curlDownload('https://www.baidu.com/', $des1, ['connect_timeout' => 5, 'timeout' => 5], false);
        $res5 = OsHelper::curlDownload('https://www.baidu.com/', $des1, ['connect_timeout' => 5, 'timeout' => 5], true);
        $res6 = OsHelper::curlDownload('https://www.baidu.com/', $des2, ['connect_timeout' => 5, 'timeout' => 5], false);

        $this->assertFalse($res1);
        $this->assertNotEmpty($res2);
        $this->assertFalse($res3);
        $this->assertTrue($res4);
        $this->assertNotEmpty($res5);
        $this->assertFalse($res6);
    }


    public function testIsCliMode() {
        $chk = OsHelper::isCliMode();
        $this->assertTrue($chk);
    }


    public function testRunCommand() {
        $dir = TESTDIR;
        if (OsHelper::isWindows()) {
            $dir     = str_replace('/', '\\', $dir);
            $command = "dir {$dir}";
        } else {
            $command = "ls -l {$dir}";
        }

        $res0 = OsHelper::runCommand("");
        $res1 = OsHelper::runCommand($command);

        $this->assertEmpty($res0);
        $this->assertNotEmpty($res1);
    }


    public function testIsAjax() {
        $header1 = [
            'User-Agent'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $header2 = [
            'Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8',
            'Connection'      => 'keep-alive',
        ];

        $res1 = OsHelper::isAjax();
        $res2 = OsHelper::isAjax($header1);
        $res3 = OsHelper::isAjax($header2);

        $this->assertFalse($res1);
        $this->assertTrue($res2);
        $this->assertFalse($res3);
    }


    public function testIsSsl() {
        $server1 = [
            'HTTP_HOST'       => 'test.loc',
            'SERVER_NAME'     => 'test.loc',
            'SERVER_PORT'     => '80',
            'SERVER_ADDR'     => '127.0.0.1',
            'REQUEST_SCHEME'  => 'http',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ];
        $server2 = [
            'HTTP_HOST'       => 'test.com',
            'SERVER_NAME'     => 'test.com',
            'SERVER_PORT'     => '443',
            'SERVER_ADDR'     => '127.0.0.1',
            'HTTPS'           => 'on',
            'REQUEST_SCHEME'  => 'https',
            'SERVER_PROTOCOL' => 'HTTP/2.0',
        ];

        $res1 = OsHelper::isSsl($server1);
        $res2 = OsHelper::isSsl($server2);

        $this->assertFalse($res1);
        $this->assertTrue($res2);
    }


    public function testRemoteFileExists() {
        $url1 = 'https://www.baidu.com';
        $url2 = 'https://www.baidu.com/img/no.gif';

        $res1 = OsHelper::remoteFileExists($url1);
        $res2 = OsHelper::remoteFileExists($url2, ['timeout' => 10, 'connect_timeout' => 2]);

        $this->assertTrue($res1);
        $this->assertFalse($res2);
    }


}