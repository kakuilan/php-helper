<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/27
 * Time: 13:42
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\FileHelper;
use Kph\Helpers\OsHelper;
use Kph\Helpers\StringHelper;
use Kph\Helpers\ValidateHelper;
use Kph\Tests\Objects\BaseCls;
use Kph\Tests\Objects\StrictCls;

class ValidateHelperTest extends TestCase {


    public static $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
        'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7',
        'Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B405 Safari/531.21.10',
        'Mozilla/5.0 (Linux; Android 9; SM-G950F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.157 Mobile Safari/537.36',
        'Mozilla/5.0 (Linux; Android 4.2.2; HUAWEI P6-U06 Build/HuaweiP6-U06) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5',
        'Mozilla/5.0 (X11; BSD Four) AppleWebKit/534.34 (KHTML, like Gecko) wkhtmltoimage Safari/534.34',
        'Mozilla/5.0 (compatible; Konqueror/4.5; NetBSD 5.0.2; X11; amd64; en_US) KHTML/4.5.4 (like Gecko)',
        'Mozilla/5.0 (Unix) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7',
        'Mozilla/5.0 (X11; U; Unix; en-US) AppleWebKit/537.15 (KHTML, like Gecko) Chrome/24.0.1295.0 Safari/537.15 Surf/0.6',
        'Opera/9.63 (X11; FreeBSD 7.1-RELEASE i386; U; en) Presto/2.1.1',
        'Mozilla/5.0 (iPod touch; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36 Edg/80.0.361.50',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Safari/605.1.15',
        'Mozilla/5.0 (compatible; Windows NT 6.1; WOW64; IA64; en) AppleWebKit/599.0+ Maxthon/5.3 Chrome/80.0.3987.122 QupZilla/2.2.6',
        'Lynx/2.8.7rel.2 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/1.0.0a',
        'Lynx/2.8.5rel.1 libwww-FM/2.15FC SSL-MM/1.4.1c OpenSSL/0.9.7e-dev',
        'w3m/0.5.2 (Linux i686; en; Debian-3.0.6-3)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763',
    ];


    public function testIsInteger() {
        $res1 = ValidateHelper::isInteger(213);
        $res2 = ValidateHelper::isInteger(9223372036854775807);
        $res3 = ValidateHelper::isInteger(92233720368547758070, false);
        $res4 = ValidateHelper::isInteger('92233720368547758070', true);
        $res5 = ValidateHelper::isInteger(92233720368547758070, true);
        $res6 = ValidateHelper::isInteger('hello');

        $this->assertTrue($res1);
        $this->assertTrue($res2);
        $this->assertFalse($res3);
        $this->assertTrue($res4);
        $this->assertFalse($res5);
        $this->assertFalse($res6);
    }


    public function testIsFloat() {
        $tests = [
            [0, false],
            [1.2, true],
            ['3.14', true],
            ['hello', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isFloat($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsOdd() {
        $tests = [
            [-2, false],
            [3.14, false],
            [3, true],
            ['5', true],
            ['hello', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isOdd($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsEven() {
        $tests = [
            [-2, true],
            [3.14, false],
            [3, false],
            ['4', true],
            ['hello', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isEven($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsJson() {
        $tests = [
            ['', false],
            ['hello', false],
            [123, false],
            ['{"id":"1"}', true],
            ['[{"Name":"Bob","Age":32,"Company":"IBM","Engineer":true},{"Name":"John","Age":20,"Company":"Oracle","Engineer":false},{"Name":"Henry","Age":45,"Company":"Microsoft","Engineer":false}]', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isJson($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsBinary() {
        $str    = 'hello world';
        $strBin = StringHelper::str2Bin($str);
        $file   = OsHelper::getPhpPath();
        $cont   = file_get_contents($file);

        $tests = [
            [-2, false],
            [$str, false],
            [$strBin, false],
            [$cont, true],
        ];

        foreach ($tests as $test) {
            $expected = ValidateHelper::isBinary($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsEmail() {
        $tests = [
            ['as', false],
            ['a@b.c', false],
            ['hello-world@c', false],
            ['email@x-unkown-domain.com', true],
            ['copyright@github.com', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isEmail($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsMobilecn() {
        $tests = [
            ['', false],
            [123456, false],
            ['hello', false],
            [13712345678, true],
            ['13812345679', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isMobilecn($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsTel() {
        $tests = [
            ['', false],
            ['hello', false],
            [123456, false],
            ['10086', false],
            ["010-88888888", true],
            ["021-87888822", true],
            ["0511-4405222", true],
            ["021-44055520-555", true],
            ["020-89571800-125", true],
            ["400-020-9800", true],
            ["400-999-0000", true],
            ["4006-589-589", true],
            ["4007005606", true],
            ["4000631300", true],
            ["400-6911195", true],
            ["800-4321", false],
            ["8004-321", false],
            ["8004321999", true],
            ["8008676014", true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isTel($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsPhone() {
        $tests = [
            ['', false],
            ['hello', false],
            ["010-88888888", true],
            ["13712345678", true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isPhone($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsUrl() {
        $tests = [
            ['', false],
            ['hello', false],
            ["a.com", false],
            ["http://192.168.1.2:8080/abc/hell?name=li&age=4", true],
            ["http://google.com", true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isUrl($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsChinaCreditNo() {
        $tests = [
            ['', false],
            ['hello', false],
            [103456789012345678, false],
            [130503670401001, true],
            [331511199911154000, false],
            ['410107199109141000', false],
            [120105201901018140, true],
            ['220103201901014845', true],
            ['51343620190101690X', true],
            ['13020919930405236X', true],
            [140721199304324027, false],
            [140721199304054027, true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isChinaCreditNo($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsUtf8() {
        $tests = [
            ['', true],
            ['FÃ©dÃ©ration Camerounaise de Football', true],
            ['hello', true],
            ['hello world.你好，世界！', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isUtf8($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsAscii() {
        $tests = [
            ['', true],
            ['FÃ©dÃ©ration Camerounaise de Football', false],
            ['hello world.', true],
            ['hello world.你好，世界！', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isAscii($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsChinese() {
        $tests = [
            ['', false],
            ['FÃ©dÃ©ration Camerounaise de Football', false],
            ['hello world.', false],
            ['hello world.你好，世界！', false],
            ['你好世界', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isChinese($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testHasChinese() {
        $tests = [
            ['', false],
            ['FÃ©dÃ©ration Camerounaise de Football', false],
            ['hello world.', false],
            ['hello world.你好，世界！', true],
            ['你好世界', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::hasChinese($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsLetter() {
        $res1 = ValidateHelper::isLetter('');
        $res2 = ValidateHelper::isLetter('FÃ©dÃ©ration Camerounaise de Football');
        $res3 = ValidateHelper::isLetter('helloWorld');
        $res4 = ValidateHelper::isLetter('hello world.你好，世界！');
        $res5 = ValidateHelper::isLetter('hello', 1);
        $res6 = ValidateHelper::isLetter('WORLD', 2);

        $this->assertFalse($res1);
        $this->assertFalse($res2);
        $this->assertTrue($res3);
        $this->assertFalse($res4);
        $this->assertTrue($res5);
        $this->assertTrue($res6);
    }


    public function testhasLetter() {
        $tests = [
            ['', false],
            ['FÃ©dÃ©ration Camerounaise de Football', true],
            ['hello world.', true],
            ['hello world.你好，世界！', true],
            ['你好世界', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::hasLetter($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsUpperLetter() {
        $tests = [
            ['', false],
            ['FÃ©dÃ©ration Camerounaise de Football', false],
            ['HELLOWORLD', true],
            ['hello world.你好，世界！', false],
            ['你好世界', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isUpperLetter($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsLowerLetter() {
        $tests = [
            ['', false],
            ['FÃ©dÃ©ration Camerounaise de Football', false],
            ['helloworld', true],
            ['hello world.你好，世界！', false],
            ['你好世界', false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isLowerLetter($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsWord() {
        $tests = [
            ['', false],
            ['_Football', false],
            [' 3.124', false],
            ['hello world.你好，世界！', false],
            ['世界', true],
            ['hello', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isWord($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsDate2time() {
        $tests = [
            ['', false],
            ["hello", false],
            ["0000", true],
            ["1970", true],
            ["1990-01", true],
            ["1990/01", true],
            ["1990-01-02", true],
            ["1990/01/02", true],
            ["1990-01-02 03", true],
            ["1990/01/02 03", true],
            ["1990-01-02 03:14", true],
            ["1990/01/02 03:14", true],
            ["1990-01-02 03:14:59", true],
            ["1990/01/02 03:14:59", true],
            ["2990-00-00 03:14:59", false], //2989-11-30
            ["2020-02-30 03:14:59", false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isDate2time($test[0]);
            $this->assertEquals($test[1], boolval($expected));
        }
    }


    public function testStartsWith() {
        $tests = [
            ['', '', false, false],
            ['hello世 world.界，你好yeh', 'hello', false, true],
            ['hello世 world.界，你好yeh', 'hello世', false, true],
            ['hello世 world.界，你好yeh', 'Hello世', false, false],
            ['hello世 world.界，你好yeh', 'Hello世', true, true],
            ['hello世 world.界，你好yeh', 'world', false, false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::startsWith($test[0], $test[1], $test[2]);

            $this->assertEquals($test[3], $expected);
        }
    }


    public function testEndsWith() {
        $tests = [
            ['', '', false, false],
            ['hello世 world.界，你好yeh', 'yeh', false, true],
            ['hello世 world.界，你好yeh', '好yeh', false, true],
            ['hello世 world.界，你好yeh', '好Yeh', false, false],
            ['hello世 world.界，你好yeh', '好Yeh', true, true],
            ['hello世 world.界，你好yeh', 'world', false, false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::endsWith($test[0], $test[1], $test[2]);
            $this->assertEquals($test[3], $expected);
        }
    }


    public function testIsIPhoneClient() {
        $tests = [
            [self::$userAgents[0], false],
            [self::$userAgents[1], false],
            [self::$userAgents[2], true],
            [self::$userAgents[3], false],
            [self::$userAgents[4], false],
            [self::$userAgents[5], false],
        ];

        foreach ($tests as $test) {
            $expected = ValidateHelper::isIPhoneClient($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsIPadClient() {
        $tests = [
            [self::$userAgents[0], false],
            [self::$userAgents[1], false],
            [self::$userAgents[2], false],
            [self::$userAgents[3], true],
            [self::$userAgents[4], false],
            [self::$userAgents[5], false],
        ];

        foreach ($tests as $test) {
            $expected = ValidateHelper::isIPadClient($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsIOSClient() {
        $tests = [
            [self::$userAgents[0], false],
            [self::$userAgents[1], false],
            [self::$userAgents[2], true],
            [self::$userAgents[3], true],
            [self::$userAgents[4], false],
            [self::$userAgents[5], false],
        ];

        foreach ($tests as $test) {
            $expected = ValidateHelper::isIOSClient($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsAndroidClient() {
        $tests = [
            [self::$userAgents[0], false],
            [self::$userAgents[1], false],
            [self::$userAgents[2], false],
            [self::$userAgents[3], false],
            [self::$userAgents[4], true],
            [self::$userAgents[5], true],
        ];

        foreach ($tests as $test) {
            $expected = ValidateHelper::isAndroidClient($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsBase64Image() {
        $img = TESTDIR . 'data/php_elephant.png';
        $str = FileHelper::img2Base64($img);

        $res1 = ValidateHelper::isBase64Image('hello world');
        $res2 = ValidateHelper::isBase64Image($str);
        $res3 = ValidateHelper::isBase64Image('hello base64 world');

        $this->assertFalse($res1);
        $this->assertNotEmpty($res2);
        $this->assertEquals('png', $res2[2]);
        $this->assertFalse($res3);
    }


    public function testIsImage() {
        $tests = [
            ['', false],
            ['hello world.你好，世界！', false],
            ['data/php_elephant.png', true],
            ['http://test.com/img/logo.jpg', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isImage($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsExecuteFile() {
        $tests = [
            ['', false],
            ['data/php_elephant.png', false],
            [__FILE__, true],
            ['src/test.py', true]
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isExecuteFile($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsIPv4() {
        $tests = [
            ['', false],
            ["8.9.10.11", true],
            ["192.168.1.2", true],
            ["192.168.0.1:80", false],
            ["::FFFF:C0A8:1", false],
            ["fe80::2c04:f7ff:feaa:33b7", false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isIPv4($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsIPv6() {
        $tests = [
            ['', false],
            ["8.9.10.11", false],
            ["192.168.1.2", false],
            ["192.168.0.1:80", false],
            ["::FFFF:C0A8:1", true],
            ["fe80::2c04:f7ff:feaa:33b7", true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isIPv6($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsEmptyObject() {
        $obj1 = new StrictCls();
        $obj2 = new \stdClass();
        $obj3 = (object)[];
        $obj4 = new BaseCls();

        $tests = [
            [$obj1, false],
            [$obj2, true],
            [$obj3, true],
            [$obj4, false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isEmptyObject($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsIntranetIp() {
        $tests = [
            ['', false],
            ["8.9.10.11", false],
            ["192.168.1.2", true],
            ["220.181.38.148", false],
            ["172.17.0.1", true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isIntranetIp($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsSpace() {
        $tests = [
            ['', false],
            ["    　", true],
            ["    　\r\n\t", false],
            ["hello     world", false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isSpace($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsWhitespace() {
        $tests = [
            ['', false],
            ["    　", true],
            ["    　\r\n\t", true],
            ["hello     world", false],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isWhitespace($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsMultibyte() {
        $tests = [
            ['', false],
            [" ", false],
            [" \r\n\t", false],
            ['abc', false],
            ['123', false],
            ['<>@;.-=', false],
            ["hello ,world", false],
            ['ひらがな・カタカナ、．漢字', true],
            ['你好，世界 foobar', true],
            ['test@＠example.com', true],
            ['1234abcDEｘｙｚ', true],
            ['안녕하세요', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isMultibyte($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testIsQQ() {
        $tests = [
            ['', false],
            ['12345', false],
            ['123456', true],
            ['012345', false],
            ['23456789', true],
        ];
        foreach ($tests as $test) {
            $expected = ValidateHelper::isQQ($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }



}