<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/21
 * Time: 13:17
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\EncryptHelper;
use Kph\Helpers\StringHelper;


class EncryptHelperTest extends TestCase {

    public function testBase64UrlEncodeDecode() {
        $str  = "https://tool.google.com.net/encrypt?type=4Hello World! 你好！";
        $res1 = EncryptHelper::base64UrlEncode($str);

        $this->assertEquals($res1, 'aHR0cHM6Ly90b29sLmdvb2dsZS5jb20ubmV0L2VuY3J5cHQ_dHlwZT00SGVsbG8gV29ybGQhIOS9oOWlve-8gQ');

        $res2 = EncryptHelper::base64UrlDecode($res1);
        $this->assertEquals($res2, $str);
    }


    public function testAuthcode() {
        $origin = 'hello world!';
        $key    = '123456';

        $enres = EncryptHelper::authcode($origin, $key, true, 3600);
        $deres = EncryptHelper::authcode($enres[0], $key, false);
        $this->assertEquals($origin, $deres[0]);
        $this->assertEquals($enres[1], $deres[1]);

        $res1 = EncryptHelper::authcode('', '', true);
        $res2 = EncryptHelper::authcode('', '', false);
        $this->assertEquals('', $res1[0]);
        $this->assertEquals('', $res2[0]);

        $res3 = EncryptHelper::authcode('hello', $key, false);
        $this->assertEquals('', $res3[0]);

        $res4 = EncryptHelper::authcode('681ff2aaPIUK-k3oHs4StYD', $key, false);
        $this->assertEquals('', $res4[0]);
    }


    public function testEasyEncryptDecrypt() {
        $origin = 'hello world!你好，世界！';
        $key    = '123456';

        $enres = EncryptHelper::easyEncrypt($origin, $key);
        $deres = EncryptHelper::easyDecrypt($enres, $key);
        $this->assertEquals($origin, $deres);

        $res1 = EncryptHelper::easyEncrypt('', $key);
        $this->assertEquals('', $res1);

        $res2 = EncryptHelper::easyDecrypt('', $key);
        $this->assertEquals('', $res2);

        $res3 = EncryptHelper::easyDecrypt('0adc39zZaczdODqqimpcaCGfYBRwciJPLxFO3NTce8VfS5', $key);
        $this->assertEquals('', $res3);

        $res4 = EncryptHelper::easyDecrypt('e10adc39   ', $key);
        $this->assertEquals('', $res4);

        $str  = implode('', range(0, 99));
        $res5 = EncryptHelper::easyEncrypt($str, $key);
        $res6 = EncryptHelper::easyDecrypt($res5, $key);
        $this->assertEquals($str, $res6);
    }


    public function testMurmurhash3Int() {
        $origin = 'hello';
        $res1   = EncryptHelper::murmurhash3Int($origin);
        $res2   = EncryptHelper::murmurhash3Int($origin, 3, false);
        $this->assertEquals(11, strlen($res1));
        $this->assertEquals(10, strlen($res2));

        $origin .= '2';
        $res3   = EncryptHelper::murmurhash3Int($origin);
        $origin .= '3';
        $res4   = EncryptHelper::murmurhash3Int($origin);
    }


    public function testOpensslEncryptDecrypt() {
        $str = 'hello world.';
        $key = 'Ti*1@^LSxg1E#^Gc';
        $iv  = '37nCVPl5HtTKYBqW';

        $res0 = EncryptHelper::opensslEncrypt('', '');
        $res1 = EncryptHelper::opensslDecrypt('', '');
        $this->assertEmpty($res0);
        $this->assertEmpty($res1);

        $cipherText1 = EncryptHelper::opensslEncrypt($str, $key);
        $cipherText2 = EncryptHelper::opensslEncrypt($str, $key, $iv);
        $cipherText3 = EncryptHelper::opensslEncrypt($str, $key, $iv, 'aes-256-cbc');
        $cipherText4 = EncryptHelper::opensslEncrypt($str, $key, $iv, 'des-ede3-cbc');

        $clearText1 = EncryptHelper::opensslDecrypt($cipherText1, $key);
        $clearText2 = EncryptHelper::opensslDecrypt($cipherText2, $key, $iv);
        $clearText3 = EncryptHelper::opensslDecrypt($cipherText3, $key, $iv, 'aes-256-cbc');
        $clearText4 = EncryptHelper::opensslDecrypt($cipherText4, $key, $iv, 'des-ede3-cbc');

        $this->assertEquals($str, $clearText1);
        $this->assertEquals($str, $clearText2);
        $this->assertEquals($str, $clearText3);
        $this->assertEquals($str, $clearText4);

    }


}