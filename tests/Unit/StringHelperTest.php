<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/26
 * Time: 13:26
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Kph\Helpers\FileHelper;
use Kph\Helpers\StringHelper;
use Kph\Helpers\ValidateHelper;


class StringHelperTest extends TestCase {


    public static $html = <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>This is page title</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/assets/css/frontend.min.css?v=0.0.1" rel="stylesheet">
    <link href="/assets/css/all.css?v=0.0.1" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        a{
            color: red;
        }
        span{
            margin: 5px;
        }
    </style>
</head>
<body>
    <div>
        <img src="/assets/img/nf.jpg" alt="this is image" class="fleft">
        <div class="fleft">最新公告</div>
        <div class="fright">
            <a href="logout" class="logoutBtn" style="display: none">退出</a>
            <a href="javascript:;" class="loginPwdBtn">登录</a>
            <a href="javascript:;" class="regisBtn">注册</a>
        </div>
        <h1>This is H1 title.</h1>
        <div>
            <p>
                Hello world!
                <span>TEXT <b>I</b> WANT</span>
            </p>
            <ul>
                <li><a href="foo">Foo</a><li>
                <a href="/bar/baz">BarBaz</a>
            </ul>

            <form name="query" action="http://www.example.net" method="post">
                <input type="text" value="123" />
                <textarea type="text" name="nameiknow">The text I want</textarea>
                <select>
                    <option value="111">111</option>
                    <option value="222">222</option>
                </select>
                <canvas>hello</canvas>
                <div id="button">
                    <input type="submit" value="Submit" />
                    <button>提交按钮</button>
                </div>
            </form>
        </div>
        <div>
            <iframe src="http://google.com"></iframe>
        </div>
    </div>
    <script type="text/javascript">
        var require = {
            config: {
                "modulename": "index",
                "controllername": "index",
                "actionname": "index",
                "jsname": "index",
                "moduleurl": "demo",
                "language": "zh-cn",
                "__PUBLIC__": "/",
                "__ROOT__": "/",
                "__CDN__": ""
            }
        };
        /* <![CDATA[ */
        var post_notif_widget_ajax_obj = {"ajax_url":"http:\/\/site.com\/wp-admin\/admin-ajax.php","nonce":"9b8270e2ef","processing_msg":"Processing..."};
        /* ]]> */
    </script>
    <script src="/assets/js/require.min.js" data-main="/assets/js/require-frontend.min.js?v=0.0.1"></script>
</body>
</html>
EOF;


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
        $res3 = StringHelper::randNumber(20);

        $this->assertEquals(6, strlen($res1));
        $this->assertEquals(10, strlen($res2));
        $this->assertEquals(20, strlen($res3));
    }


    public function testRandString() {
        $res1 = StringHelper::randString();
        $res2 = StringHelper::randString(10, 1);
        $res3 = StringHelper::randString(10, 2);
        $res4 = StringHelper::randString(10, 3);
        $res5 = StringHelper::randString(10, 4);
        $res6 = StringHelper::randString(10, 5);
        $res7 = StringHelper::randString(10, 0, '!@#$%^&*');
        $res8 = StringHelper::randString(60);

        $this->assertEquals(6, strlen($res1));
        $this->assertTrue(ValidateHelper::isLetter($res2));
        $this->assertTrue(is_numeric($res3));
        $this->assertTrue(ValidateHelper::isUpperLetter($res4));
        $this->assertTrue(ValidateHelper::isLowerLetter($res5));
        $this->assertTrue(ValidateHelper::isChinese($res6));
        $this->assertEquals(10, strlen($res7));
        $this->assertEquals(60, strlen($res8));
    }


    public function testFixHtml() {
        $str1 = '这是一段被截断的html文本<a href="#"';
        $str2 = '这是一段被截断的html文本<a href="#">';
        $str3 = <<<EOF
    <html>
     <head>
      <title>test</title>
     </head>
     <body>
      <p>error</i>
     </body>
    </html>
EOF;

        $res1 = StringHelper::fixHtml($str1);
        $res2 = StringHelper::fixHtml($str2);
        $res3 = StringHelper::fixHtml('hello');
        $res4 = StringHelper::fixHtml($str3);

        $this->assertGreaterThan(0, stripos($res1, '</a>'));
        $this->assertGreaterThan(0, stripos($res2, '</a>'));
        $this->assertEquals('<p>hello</p>', $res3);
        $this->assertGreaterThan(0, stripos($res4, 'DOCTYPE'));
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

        array_push($arr, $str);
        $res = StringHelper::getClosestWord($str, $arr);
        $this->assertEquals($str, $res[0]);
        $this->assertEquals(0, $res[1]);
    }


    public function testEscapeUnescape() {
        $str = 'Some \' problematic \\ chars " ... ?wd=博客&name=张 三&age=20&qu=\'quote\'';

        $res1 = StringHelper::escape($str);
        $res2 = StringHelper::unescape($res1);
        $this->assertEquals($str, $res2);

        $str2 = 'I will &#x2702;display &#9986;';
        $res3 = StringHelper::unescape($str2);
        $this->assertEquals(17, mb_strlen($res3));

    }


    public function testGetFirstLetter() {
        $tests = [
            ['', ''],
            ['-~!@#$', ''],
            ['「喜」', ''],
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


    public function testBr2nl() {
        $str = 'hello <br/>world<br >你好';
        $res = StringHelper::br2nl($str);
        $this->assertFalse(stripos($res, 'br'));
    }


    public function testRemoveSpace() {
        $str  = <<<EOF
        hello World&nbsp;   你
        how 　are you?　\r\n\t
        好，世 界   
        ！呵　呵
EOF;
        $res1 = StringHelper::removeSpace('');
        $res2 = StringHelper::removeSpace($str, true);
        $res3 = StringHelper::removeSpace($str, false);

        $this->assertEmpty($res1);
        $this->assertEquals('helloWorld你howareyou?好，世界！呵呵', $res2);
        $this->assertEquals('hello World&nbsp; 你 how are you? 好，世 界 ！呵　呵', $res3);
    }


    public function testGetText() {
        $res = StringHelper::getText(self::$html);
        $this->assertNotEmpty($res);

        $this->assertEmpty(StringHelper::getText(''));
    }


    public function testRemoveHtml() {
        $res = StringHelper::removeHtml(self::$html);
        $this->assertNotEmpty($res);
        $this->assertEmpty(StringHelper::removeHtml(''));
    }


    public function testStringWordCount() {
        $str  = 'hello ,world.你好，世　界！&nbsp;coder.&lt;&gt;字符实体。';
        $res1 = StringHelper::stringWordCount($str, 0);
        $res2 = StringHelper::stringWordCount($str, 1);
        $res3 = StringHelper::stringWordCount($str, 2);

        $this->assertEquals(3, $res2);
        $this->assertEquals(11, $res3);
        $this->assertGreaterThan($res3, $res1);
    }


    public function testHideCard() {
        $str1 = '331511199';
        $str2 = '331511199911154000';
        $res1 = StringHelper::hideCard('3315');
        $res2 = StringHelper::hideCard($str1);
        $res3 = StringHelper::hideCard($str2);

        $this->assertEquals('******', $res1);
        $this->assertNotEquals($str1, $res2);
        $this->assertNotEquals($str2, $res3);
    }


    public function testHideMobile() {
        $str  = '13812345678';
        $res1 = StringHelper::hideMobile('0755123');
        $res2 = StringHelper::hideMobile($str);

        $this->assertEquals('***', $res1);
        $this->assertNotEquals($str, $res2);
    }


    public function testHideTrueName() {
        $tests = [
            ['', '**'],
            ['李四', '李**'],
            ['张三丰', '张**'],
            ['公孙先生', '公孙**'],
            ['helloWorld', 'hel***rld'],
            ['北京搜狗科技公司', '北京***公司'],
            ['北京搜狗科技发展有限公司', '北京搜***限公司'],
            ['工商发展银行深圳南山科苑梅龙路支行', '工商发展***龙路支行'],
        ];

        foreach ($tests as $test) {
            $expected = StringHelper::hideTrueName($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testCountBase64Byte() {
        $img = TESTDIR . 'data/php_elephant.png';
        $str = FileHelper::img2Base64($img);

        $res1 = StringHelper::countBase64Byte('');
        $res2 = StringHelper::countBase64Byte($str);

        $this->assertEquals(0, $res1);
        $this->assertGreaterThan(10000, $res2);
    }


    public function testStrXBin() {
        $str = 'hello world.你好，世界！';

        $res1 = StringHelper::str2Bin($str);
        $res2 = StringHelper::bin2Str($res1);

        $this->assertNotEmpty($res1);
        $this->assertEquals($str, $res2);
    }


    public function testMultiExplode() {
        $str = 'hello world.你好，世　界';

        $res = StringHelper::multiExplode($str, ...[' ', '.', '，', '　']);
        $this->assertEquals(5, count($res));

        $res2 = StringHelper::multiExplode('');
        $res3 = StringHelper::multiExplode($str);
        $this->assertEmpty($res2);
        $this->assertEquals($str, $res3[0]);
    }


    public function testRemoveEmoji() {
        $str = 'Lorem ipsum 🥊dolor 🤒sit amet, consectetur adipiscing 🍂 elit. 🍁🍃🍂🌰🍁🌿🌾🌼🌻سلام تست شد hell中文';
        $str .= '😀😁😂😃😄😅😆😉😊😋😎😍😘😗😙😚☺😇😐😑😶😏😣😥😮😯😪😫😴😌😛😜😝😒😓😔😕😲😷😖😞😟😤😢😭😦😧😨😬😰😱😳😵😡😠';
        $str .= '👦👧👨👩👴👵👶👱👮👲👳👷👸💂🎅👰👼💆💇🙍🙎🙅🙆💁🙋🙇🙌🙏👤👥🚶🏃👯💃👫👬👭💏💑👪';
        $str .= '💪👈👉☝👆👇✌✋👌👍👎✊👊👋👏👐✍';
        $str .= '👣👀👂👃👅👄💋👓👔👕👖👗👘👙👚👛👜👝🎒💼👞👟👠👡👢👑👒🎩🎓💄💅💍🌂';
        $str .= '📱📲📶📳📴☎📞📟📠';
        $str .= '♻🏧🚮🚰♿🚹🚺🚻🚼🚾⚠🚸⛔🚫🚳🚭🚯🚱🚷🔞💈';
        $str .= '🙈🙉🙊🐵🐒🐶🐕🐩🐺🐱😺😸😹😻😼😽🙀😿😾🐈🐯🐅🐆🐴🐎🐮🐂🐃🐄🐷🐖🐗🐽🐏🐑🐐🐪🐫🐘🐭🐁🐀🐹🐰🐇🐻🐨🐼🐾🐔🐓🐣🐤🐥🐦🐧🐸🐊🐢🐍🐲🐉🐳🐋🐬🐟🐠🐡🐙🐚🐌🐛🐜🐝🐞';
        $str .= '💐🌸💮🌹🌺🌻🌼🌷🌱🌲🌳🌴🌵🌾🌿🍀🍁🍂🍃';
        $str .= '🌍🌎🌏🌐🌑🌒🌓🌔🌕🌖🌗🌘🌙🌚🌛🌜☀🌝🌞⭐🌟🌠☁⛅☔⚡❄🔥💧🌊';
        $str .= '🍇🍈🍉🍊🍋🍌🍍🍎🍏🍐🍑🍒🍓🍅🍆🌽🍄🌰🍞🍖🍗🍔🍟🍕🍳🍲🍱🍘🍙🍚🍛🍜🍝🍠🍢🍣🍤🍥🍡🍦🍧🍨🍩🍪🎂🍰🍫🍬🍭🍮🍯🍼☕🍵🍶🍷🍸🍹🍺🍻🍴';
        $str .= '🎪🎭🎨🎰🚣🛀🎫🏆⚽⚾🏀🏈🏉🎾🎱🎳⛳🎣🎽🎿🏂🏄🏇🏊🚴🚵🎯🎮🎲🎷🎸🎺🎻🎬';
        $str .= '😈👿👹👺💀☠👻👽👾💣';
        $str .= '🌋🗻🏠🏡🏢🏣🏤🏥🏦🏨🏩🏪🏫🏬🏭🏯🏰💒🗼🗽⛪⛲🌁🌃🌆🌇🌉🌌🎠🎡🎢🚂🚃🚄🚅🚆🚇🚈🚉🚊🚝🚞🚋🚌🚍🚎🚏🚐🚑🚒🚓🚔🚕🚖🚗🚘🚚🚛🚜🚲⛽🚨🚥🚦🚧⚓⛵🚤🚢✈💺🚁🚟🚠🚡🚀🎑🗿🛂🛃🛄🛅';
        $str .= '💌💎🔪💈🚪🚽🚿🛁⌛⏳⌚⏰🎈🎉🎊🎎🎏🎐🎀🎁📯📻📱📲☎📞📟📠🔋🔌💻💽💾💿📀🎥📺📷📹📼🔍🔎🔬🔭📡💡🔦🏮📔📕📖📗📘📙📚📓📃📜📄📰📑🔖💰💴💵💶💷💸💳✉📧📨📩📤📥📦📫📪📬📭📮✏✒📝📁📂📅📆📇📈📉📊📋📌📍📎📏📐✂🔒🔓🔏🔐🔑🔨🔫🔧🔩🔗💉💊🚬🔮🚩🎌💦💨';
        $str .= '♠♥♦♣🀄🎴🔇🔈🔉🔊📢📣💤💢💬💭♨🌀🔔🔕✡✝🔯📛🔰🔱⭕✅☑✔✖❌❎➕➖➗➰➿〽✳✴❇‼⁉❓❔❕❗©®™🎦🔅🔆💯🔠🔡🔢🔣🔤🅰🆎🅱🆑🆒🆓ℹ🆔Ⓜ🆕🆖🅾🆗🅿🆘🆙🆚🈁🈂🈷🈶🈯🉐🈹🈚🈲🉑🈸🈴🈳㊗㊙🈺🈵▪▫◻◼◽◾⬛⬜🔶🔷🔸🔹🔺🔻💠🔲🔳⚪⚫🔴🔵';
        $str .= '🐁🐂🐅🐇🐉🐍🐎🐐🐒🐓🐕🐖';
        $str .= '♈♉♊♋♌♍♎♏♐♑♒♓⛎';
        $str .= '🕛🕧🕐🕜🕑🕝🕒🕞🕓🕟🕔🕠🕕🕡🕖🕢🕗🕣🕘🕤🕙🕥🕚🕦⌛⏳⌚⏰⏱⏲🕰';
        $str .= '💘❤💓💔💕💖💗💙💚💛💜💝💞💟❣';
        $str .= '💐🌸💮🌹🌺🌻🌼🌷🌱🌿🍀';
        $str .= '🌿🍀🍁🍂🍃';
        $str .= '🌑🌒🌓🌔🌕🌖🌗🌘🌙🌚🌛🌜🌝';
        $str .= '🍇🍈🍉🍊🍋🍌🍍🍎🍏🍐🍑🍒🍓';
        $str .= '💴💵💶💷💰💸💳';
        $str .= '🚂🚃🚄🚅🚆🚇🚈🚉🚊🚝🚞🚋🚌🚍🚎🚏🚐🚑🚒🚓🚔🚕🚖🚗🚘🚚🚛🚜🚲⛽🚨🚥🚦🚧⚓⛵🚣🚤🚢✈💺🚁🚟🚠🚡🚀';
        $str .= '🏠🏡🏢🏣🏤🏥🏦🏨🏩🏪🏫🏬🏭🏯🏰💒🗼🗽⛪🌆🌇🌉';
        $str .= '📱📲☎📞📟📠🔋🔌💻💽💾💿📀🎥📺📷📹📼🔍🔎🔬🔭📡📔📕📖📗📘📙📚📓📃📜📄📰📑🔖💳✉📧📨📩📤📥📦📫📪📬📭📮✏✒📝📁📂📅📆📇📈📉📊📋📌📍📎📏📐✂🔒🔓🔏🔐🔑';
        $str .= '⬆↗➡↘⬇↙⬅↖↕↔↩↪⤴⤵🔃🔄🔙🔚🔛🔜🔝';

        $res1 = StringHelper::removeEmoji('');
        $res2 = StringHelper::removeEmoji($str);

        $this->assertEmpty($res1);
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing  elit. سلام تست شد hell中文', $res2);
    }


    public function testToCamelCase() {
        $tests = [
            ['', ''],
            ['some_words', 'SomeWords'],
            ['http_server', 'HttpServer'],
            ['no_https', 'NoHttps'],
            ['_complex__case_', '_Complex_Case_'],
            ['some words', 'SomeWords'],
            ['sayHello', 'SayHello'],
            ['SayHello', 'SayHello'],
            ['SayHelloWorld', 'SayHelloWorld'],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::toCamelCase($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testToSnakeCase() {
        $tests = [
            ['', ''],
            ["FirstName", "first_name"],
            ["HTTPServer", "http_server"],
            ["NoHTTPS", "no_https"],
            ["GO_PATH", "go_path"],
            ["GO PATH", "go_path"],
            ["GO-PATH", "go_path"],
            ["HTTP2XX", "http_2xx"],
            ["http2xx", "http_2xx"],
            ["HTTP20xOK", "http_20x_ok"],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::toSnakeCase($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testToKebabCase() {
        $tests = [
            ['', ''],
            ["你helloWorld", "你hello-world"],
            ["A", "a"],
            ["HellOW好orld", "hell-o-w好orld"],
            ["-FirstName", "-first-name"],
            ["FirstName", "first-name"],
            ["HTTPServer", "http-server"],
            ["NoHTTPS", "no-https"],
            ["GO_PATH", "go-path"],
            ["GO PATH", "go-path"],
            ["GO-PATH", "go-path"],
            ["HTTP2XX", "http-2xx"],
            ["http2xx", "http-2xx"],
            ["HTTP20xOK", "http-20x-ok"],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::toKebabCase($test[0]);
            $this->assertEquals($test[1], $expected);
        }
    }


    public function testToArray() {
        $str1 = 'hello, world';
        $str2 = 'hello你好，世界world！';

        $res1 = StringHelper::toArray($str1);
        $res2 = StringHelper::toArray($str2);

        $this->assertEquals(12, count($res1));
        $this->assertEquals(16, count($res2));
    }


    public function testDstrpos() {
        $str = 'hello world. 你好，世界！';
        $arr = ['php', 'Hello', 'today'];

        $res1 = StringHelper::dstrpos($str, $arr, false, false);
        $this->assertTrue($res1);

        $res2 = StringHelper::dstrpos($str, $arr, true, false);
        $res3 = StringHelper::dstrpos($str, $arr, true, true);
        $this->assertEquals('Hello', $res2);
        $this->assertFalse($res3);

        $res4 = StringHelper::dstrpos('', $arr);
        $this->assertFalse($res4);
    }


    public function testRemoveBefore() {
        $tests = [
            ['', '', false, false, ''],
            ['hello world learn php', 'world', false, false, 'world learn php'],
            ['hello world learn php', 'world', true, false, ' learn php'],
            ['hello world learn php', 'World', false, false, 'hello world learn php'],
            ['hello world learn php', 'World', false, true, 'world learn php'],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::removeBefore($test[0], $test[1], $test[2], $test[3]);
            $this->assertEquals($test[4], $expected);
        }
    }


    public function testRemoveAfter() {
        $tests = [
            ['', '', false, false, ''],
            ['hello world learn php', 'learn', false, false, 'hello world learn'],
            ['hello world learn php', 'learn', true, false, 'hello world '],
            ['hello world learn php', 'Learn', false, false, 'hello world learn php'],
            ['hello world learn php', 'Learn', false, true, 'hello world learn'],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::removeAfter($test[0], $test[1], $test[2], $test[3]);
            $this->assertEquals($test[4], $expected);
        }
    }


    public function testPasswdSafeGrade() {
        $tests = [
            ['', 0],
            ['12abc', 1],
            ['01234567890', 1],
            ['1223456', 1],
            ['abc123456', 1],
            ['HelloWorld', 1],
            ["abc@123456", 2],
            ["abc@123aPPT", 2],
            ['pwd@12345', 2],
            ['pwd@123aPPT', 2],
            ["tcl@123a#PPT", 3],
            ['SGVsbG8gV29ybGQhIOS9oOWlve+8gQ==', 4],
            ['bom7o++iQ,B)aWxD>a?MkmXR9', 4],
        ];
        foreach ($tests as $test) {
            $expected = $test[1];
            $actual   = StringHelper::passwdSafeGrade($test[0]);
            $this->assertEquals($expected, $actual);
        }
    }


    public function testUuidV4() {
        $res = StringHelper::uuidV4();

        $this->assertNotEmpty($res);
        $this->assertEquals(36, strlen($res));
    }


    public function testContains() {
        $str   = 'Hello world.世界，你好！';
        $tests = [
            ['', '', false, false],
            [$str, 'world', false, true],
            [$str, 'World', false, false],
            [$str, 'World', true, true],
            [$str, '哈罗', true, false],
        ];

        foreach ($tests as $test) {
            $expected = StringHelper::contains($test[0], $test[1], $test[2]);
            $this->assertEquals($test[3], $expected);
        }
    }


    public function testMiddle() {
        $str   = 'beg01 Hello world.世界，你好！结束';
        $tests = [
            ['', '', '', ''],
            [$str, '', '', $str],
            [$str, '合适', '', ''],
            [$str, '', '已经', ''],
            [$str, '', '结束', 'beg01 Hello world.世界，你好！'],
            [$str, 'beg01', '', ' Hello world.世界，你好！结束'],
            [$str, 'beg01', '已经', ''],
            [$str, 'beg01', '结束', ' Hello world.世界，你好！'],
            [$str, 'beg01', ' Hello', ''],
        ];
        foreach ($tests as $test) {
            $expected = StringHelper::middle($test[0], $test[1], $test[2]);
            $this->assertEquals($test[3], $expected);
        }
    }


    public function testStripBrackets() {
        $str = "你好(hello (are(:) )you ok?)，世界（world （呵呵）哒）！InfluxDB 公司引用了 (IDC, 2020) 的报告，边缘功能 (Edge functionality) –将电池寿命延长 1.25 小时（根据内部基准测试）。增加了一个名为 <Chrome <start>Actions>的功能。网站管理员可以通过[IntensiveWakeUpThrottling [end[tag] ] Enabled] 策略来控制此限制。<Daniel> 发现自己的推特帐号被强制退出会话（他在手机和 Linux 电脑使用了 TweetDeck 客户端）。实验性 API：BrowserView.{destroy, fromId, fromWebContents, getAllViews}，添加webContents.forcefullyCrashRenderer()以强制终止{渲染器}进程！《VUE》新语法糖魔改《JavaScript》引争议。";

        $tags = [
            '(',
            ')',
            '[',
            ']',
            '{',
            '}',
            '<',
            '>',
            '（',
            '）',
            '【',
            '】',
            '《',
            '》',
        ];

        $res0 = StringHelper::stripBrackets($str, -9);
        $res1 = StringHelper::stripBrackets($str, 1, false);
        $res2 = StringHelper::stripBrackets($str, 1, true);

        $this->assertFalse(StringHelper::dstrpos($res0, $tags));
        $this->assertFalse(StringHelper::dstrpos($res1, ['(', ')',]));
        $this->assertTrue(StringHelper::dstrpos($res2, ['(', ')',]));
    }


    public function testGrapBrackets() {
        $str = "你好(hello (are(:) )you ok?)，世界（world （呵呵）哒）！InfluxDB 公司引用了 (IDC, 2020) 的报告，边缘功能 (Edge functionality) –将电池寿命延长 1.25 小时（根据内部基准测试）。增加了一个名为 <Chrome <start>Actions>的功能。网站管理员可以通过[IntensiveWakeUpThrottling [end[tag] ] Enabled] 策略来控制此限制。<Daniel> 发现自己的推特帐号被强制退出会话（他在手机和 Linux 电脑使用了 TweetDeck 客户端）。实验性 API：BrowserView.{destroy, fromId, fromWebContents, getAllViews}，添加webContents.forcefullyCrashRenderer()以强制终止{渲染器}进程！《VUE》新语法糖魔改《JavaScript》引争议。";

        $res0 = StringHelper::grabBrackets($str, -9);
        $res1 = StringHelper::grabBrackets($str, 0, true);

        $this->assertEquals(count($res0), count($res1));
    }


    public function testToBytes() {
        $str = "Hello World! 你好，世界！Olá🐍 With Emojis 🐳📜";

        $res1 = StringHelper::toBytes($str);
        $res2 = StringHelper::bytes2Str($res1);

        $this->assertEquals(count($res1), strlen($str));
        $this->assertEquals($str, $res2);
    }


}