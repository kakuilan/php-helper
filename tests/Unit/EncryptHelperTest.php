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
use Kph\Consts;
use Kph\Helpers\EncryptHelper;
use Kph\Helpers\StringHelper;


class EncryptHelperTest extends TestCase {
    private static $strHello      = "Hello World! 你好，世界！";
    private static $strHelloEmoji = "Hello World! 你好，世界！안녕, 세계！ Olá mundo,With Emojis:😃🐳📜💯⌚";
    private static $strJson       = '{"id":9999,"url":"https://baidu.com"}';
    private static $emptyMd5      = "d41d8cd98f00b204e9800998ecf8427e";


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

        $enres = EncryptHelper::authcode($origin, $key, true, Consts::TTL_ONE_YEAR);
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

        $enres = EncryptHelper::authcode(self::$strHello, self::$emptyMd5, true, 0);
        //res:8c9eb7905a6SdXZfm-GoJpYKu6CzMgF0I-7neF-x3UKIUpYuIZSnK_2ZqaYSZlZw0Ofzwa2Bn0QZ6b4SLzSz
        $deres = EncryptHelper::authcode($enres[0], self::$emptyMd5, false);
        $this->assertEquals(self::$strHello, $deres[0]);
        $this->assertEquals($enres[1], $deres[1]);

        $enres = EncryptHelper::authcode(self::$strHelloEmoji, self::$emptyMd5, true, 0);
        //res:b42374af3DqX22zi207OJXsz6xP2vEXto39TPK_UzcJOdDZV0kQHPUFm5JOw-aWISFi0snglsrYtp5tpYGRuhgw50TPY8UnFSf912uZI38vGON0KHqAgCatmtdoBZ4VJI6IkHio-JLxbt8hkuCz1HCOElUkZxBMnGUle
        $deres = EncryptHelper::authcode($enres[0], self::$emptyMd5, false);
        $this->assertEquals(self::$strHelloEmoji, $deres[0]);
        $this->assertEquals($enres[1], $deres[1]);

        $key   = substr(self::$emptyMd5, 0, 16);
        $enres = EncryptHelper::authcode(self::$strJson, $key, true, 0);
        //res:52a0945eK4NyxvnjEBnPlToROzO4KLKE9VvrqtxAiLPVPDK-HkvzahyMbxydmSifc3TQIo4mbsi9gzq7vbJ64YzpB_DP
        $deres = EncryptHelper::authcode($enres[0], $key, false);
        $this->assertEquals(self::$strJson, $deres[0]);
        $this->assertEquals($enres[1], $deres[1]);
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