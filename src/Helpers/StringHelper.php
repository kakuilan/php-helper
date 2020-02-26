<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/21
 * Time: 16:12
 * Desc:
 */

namespace Kph\Helpers;


/**
 * Class StringHelper
 * @package Kph\Helpers
 */
class StringHelper {


    /**
     * 半角字符集
     * @var array
     */
    public static $DBCChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '-', ' ', ':', '.', ',', '/', '%', '#', '!', '@', '&', '(', ')', '<', '>', '"', '\'', '?', '[', ']', '{', '}', '\\', '|', '+', '=', '_', '^', '$', '~', '`'];


    /**
     * 全角字符集
     * @var array
     */
    public static $SBCChars = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９', 'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ', 'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ', 'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ', 'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ', 'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ', 'Ｚ', 'ａ', 'ｂ', 'ｃ', 'ｄ', 'ｅ', 'ｆ', 'ｇ', 'ｈ', 'ｉ', 'ｊ', 'ｋ', 'ｌ', 'ｍ', 'ｎ', 'ｏ', 'ｐ', 'ｑ', 'ｒ', 'ｓ', 'ｔ', 'ｕ', 'ｖ', 'ｗ', 'ｘ', 'ｙ', 'ｚ', '－', '　', '：', '．', '，', '／', '％', '＃', '！', '＠', '＆', '（', '）', '＜', '＞', '＂', '＇', '？', '［', '］', '｛', '｝', '＼', '｜', '＋', '＝', '＿', '＾', '＄', '～', '｀'];


    /**
     * md5短串(返回16位md5值)
     * @param string $str
     * @return string
     */
    public static function md5Short(string $str): string {
        return substr(md5(strval($str)), 8, 16);
    }


    /**
     * 字符串剪切(宽字符)
     * @param string $str 字符串
     * @param int $length 截取长度
     * @param int $start 开始位置
     * @param string $dot 后接的省略符
     * @return string
     */
    public static function cutStr(string $str, int $length, int $start = 0, string $dot = ''): string {
        //转换html实体
        $str = htmlspecialchars_decode($str);
        $len = mb_strlen($str, 'UTF-8');
        $str = mb_substr($str, $start, $length, 'UTF-8');

        if ($length && $length < $len - $start) {
            $str .= $dot;
        }

        return $str;
    }


    /**
     * 获取宽字符串长度函数
     * @param string $str
     * @param bool $filterTags 是否过滤(html/php)标签
     * @return int
     */
    public static function length(string $str, bool $filterTags = false): int {
        if ($filterTags) {
            $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
            $str = strip_tags($str);
        }

        return mb_strlen($str, 'UTF-8');
    }


    /**
     * 简单随机字符串
     * @param int $len 字符串长度
     * @param bool $hasSpecial 是否有特殊字符
     * @return string
     */
    public static function randSimple(int $len = 6, bool $hasSpecial = false): string {
        $chars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        if ($hasSpecial) {
            $chars .= '!@#$%^&*()_+-=`~[]{}|<>?:';
        }

        $result = '';
        $max    = strlen($chars) - 1;
        for ($i = 0; $i < $len; $i++) {
            $result .= $chars[rand(0, $max)];
        }
        return $result;
    }


    /**
     * 随机数字
     * @param int $len 字符串长度
     * @return string
     */
    public static function randNumber(int $len = 6): string {
        if ($len <= 10) {
            $arr = range(0, 9);
        } else {
            $arr = range(0, pow(10, ceil($len / 10)) - 1);
        }
        shuffle($arr);
        $str = implode('', $arr);

        return substr($str, 0, $len);
    }


    /**
     * 生成随机字串
     * @param int $len 长度
     * @param int $type 字串类型:1 不区分大小写的字母, 2 数字, 3 大写字母, 4 小写字母, 5 中文, 0 数值和字母
     * @param string $addChars 额外的随机字符
     * @return string
     */
    public static function randString(int $len = 6, int $type = 0, string $addChars = ''): string {
        $str = '';
        switch ($type) {
            case 1:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 2:
                $chars = '0123456789';
                break;
            case 3:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 4:
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 5:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借";
                break;
            case 0:
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
                break;
        }

        if (!empty($addChars)) {
            $chars .= $addChars;
        }

        //位数过长重复字符串一定次数
        $charLen = mb_strlen($chars, 'UTF-8');
        $diff    = $len / $charLen;
        if ($diff > 1) {
            $chars = str_repeat($chars, ceil($diff));
        }

        if ($type == 5) { // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= mb_substr($chars, floor(mt_rand(0, $charLen - 1)), 1, 'UTF-8');
            }
        } else {
            $chars = str_shuffle($chars);
            $str   = substr($chars, 0, $len);
        }

        return $str;
    }


    /**
     * 修复未闭合的html标签.如
     * fixHtml('这是一段被截断的html文本<a href="#"');
     * @param string $html
     * @return string
     */
    public static function fixHtml(string $html): string {
        //关闭自闭合标签
        $startPos = strrpos($html, "<");
        if (empty($html) || false == $startPos) {
            return $html;
        }

        $trimString = substr($html, $startPos);
        if (false === strpos($trimString, ">")) {
            $html = substr($html, 0, $startPos);
        }

        //非自闭合html标签列表
        preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $html, $startTags);
        preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $html, $closeTags);

        if (!empty($startTags[1]) && is_array($startTags[1])) {
            krsort($startTags[1]);
            $closeTagsIsArray = is_array($closeTags[1]);
            foreach ($startTags[1] as $key => $tag) {
                $attrLength = strlen($startTags[2][$key]);
                if ($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1])) {
                    continue;
                }
                if (!empty($closeTags[1]) && $closeTagsIsArray) {
                    if (false !== ($index = array_search($tag, $closeTags[1]))) {
                        unset($closeTags[1][$index]);
                        continue;
                    }
                }
                $html .= "</{$tag}>";
            }
        }

        return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $html);
    }


    /**
     * 半角转全角字符
     * @param string $str
     * @return string
     */
    public static function DBC2SBC(string $str): string {
        return str_replace(self::$DBCChars, self::$SBCChars, $str);
    }


    /**
     * 全角转半角字符
     * @param string $str
     * @return string
     */
    public static function SBC2DBC(string $str): string {
        return str_replace(self::$SBCChars, self::$DBCChars, $str);
    }


    /**
     * 获取相似度最高的字符串,结果是数组,包含相似字符和编辑距离.
     * @param string $word 要比较的字符串
     * @param array $searchs 要查找的字符串数组
     * @return array
     */
    public static function getClosestWord(string $word, array $searchs): array {
        $shortest = -1;
        $closest  = null;

        foreach ($searchs as $search) {
            $lev = levenshtein($word, $search);
            if ($lev == 0) { //完全相等
                $closest  = $search;
                $shortest = 0;
                break;
            }
            if ($lev <= $shortest || $shortest < 0) {
                $closest  = $search;
                $shortest = $lev;
            }
        }

        $res = [
            $closest,
            $shortest,
        ];

        return $res;
    }


    /**
     * escape编码
     * @param string $str 待编码字符串
     * @param string $charset 字符集
     * @return string
     */
    public static function escape(string $str, $charset = 'UTF-8'): string {
        preg_match_all("/[^\x{00}-\x{ff}]|[\x{00}-\x{ff}]+/u", $str, $matches);
        $arr = $matches[0] ?? [];
        foreach ($arr as $k => $v) {
            if (ord($v[0]) < 128) {
                $arr[$k] = rawurlencode($v);
            } else {
                //$arr[$k] = "%u" . bin2hex(@iconv($charset, "UCS-2", $v));
                $arr[$k] = "%u" . bin2hex(mb_convert_encoding($v, 'UCS-2', $charset));
            }
        }

        return join('', $arr);
    }


    /**
     * unescape解码
     * @param string $str 待解码字符串
     * @param string $charset 字符集
     * @return string
     */
    public static function unescape(string $str, $charset = 'UTF-8'): string {
        $str = rawurldecode($str);
        preg_match_all("/%u.{4}|&#x.{4};|&#\d+;|.+/U", $str, $matches);
        $arr = $matches[0] ?? [];

        foreach ($arr as $k => $v) {
            if (substr($v, 0, 2) == "%u") {
                $arr[$k] = mb_convert_encoding(pack("H4", substr($v, -4)), $charset, 'UCS-2');
            } elseif (substr($v, 0, 3) == "&#x") {
                $arr[$k] = mb_convert_encoding(pack("H4", substr($v, 3, -1)), $charset, 'UCS-2');
            } elseif (substr($v, 0, 2) == "&#") {
                $arr[$k] = mb_convert_encoding(pack("H4", substr($v, 2, -1)), $charset, 'UCS-2');
            }
        }

        return join('', $arr);
    }


    /**
     * 获取字符串的首字母
     * @param string $str
     * @return string
     */
    public static function getFirstLetter(string $str): string {
        $res = '';
        if (!empty($str)) {
            $firstChar = ord(strtoupper($str[0]));
            if ($firstChar >= 65 && $firstChar <= 91) {
                return strtoupper($str[0]);
            }

            //$s   = iconv("UTF-8", "gb2312", $str);
            $s   = mb_convert_encoding($str, 'gb2312');
            $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
            if ($asc >= -20319 && $asc <= -20284)
                return "A";
            if ($asc >= -20283 && $asc <= -19776)
                return "B";
            if ($asc >= -19775 && $asc <= -19219)
                return "C";
            if ($asc >= -19218 && $asc <= -18711)
                return "D";
            if ($asc >= -18710 && $asc <= -18527)
                return "E";
            if ($asc >= -18526 && $asc <= -18240)
                return "F";
            if ($asc >= -18239 && $asc <= -17923)
                return "G";
            if ($asc >= -17922 && $asc <= -17418)
                return "H";
            if ($asc >= -17417 && $asc <= -16475)
                return "J";
            if ($asc >= -16474 && $asc <= -16213)
                return "K";
            if ($asc >= -16212 && $asc <= -15641)
                return "L";
            if ($asc >= -15640 && $asc <= -15166)
                return "M";
            if ($asc >= -15165 && $asc <= -14923)
                return "N";
            if ($asc >= -14922 && $asc <= -14915)
                return "O";
            if ($asc >= -14914 && $asc <= -14631)
                return "P";
            if ($asc >= -14630 && $asc <= -14150)
                return "Q";
            if ($asc >= -14149 && $asc <= -14091)
                return "R";
            if ($asc >= -14090 && $asc <= -13319)
                return "S";
            if ($asc >= -13318 && $asc <= -12839)
                return "T";
            if ($asc >= -12838 && $asc <= -12557)
                return "W";
            if ($asc >= -12556 && $asc <= -11848)
                return "X";
            if ($asc >= -11847 && $asc <= -11056)
                return "Y";
            if ($asc >= -11055 && $asc <= -10247)
                return "Z";
        }

        return $res;
    }


    /**
     * 匹配图片(从html中提取img的地址)
     * @param string $html
     * @return array
     */
    public static function matchImages(string $html): array {
        $images = [];
        if (!empty($html)) {
            preg_match_all('/<img.*src=(.*)[>|\\s]/iU', $html, $matchs);
            if (isset($matchs[1]) && count($matchs[1]) > 0) {
                foreach ($matchs[1] as $v) {
                    $item = trim($v, "\"'"); //删除首尾的引号 ' "
                    array_push($images, $item);
                }
            }
        }

        return $images;
    }


    /**
     * br标签转换为nl
     * @param string $str
     * @return string
     */
    public static function br2nl(string $str): string {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $str);
    }


    /**
     * 移除字符串中的空格
     * @param string $str
     * @return string
     */
    public static function removeSpace(string $str): string {
        if ($str == '') {
            return '';
        }

        $str = str_replace([chr(13), chr(10), "\n", "\r", "\t", ' ', '　', '&nbsp;'], '', $str);
        $str = preg_replace("/\s/i", '', $str);
        return trim($str, " 　\t\n\r\0\x0B");
    }


    /**
     * 获取纯文本(不保留行内空格)
     * @param string $html
     * @return string
     */
    public static function getText(string $html): string {
        if ($html == '') {
            return '';
        }

        $str = strip_tags($html);

        //移除html,js,css标签
        $search = [
            "'<script[^>]*?>.*?<\/script>'si", // 去掉 javascript
            "'<style[^>]*?>.*?<\/style>'si", // 去掉 css
            "'<[/!]*?[^<>]*?>'si", // 去掉 HTML 标记
            "'<!--[/!]*?[^<>]*?>'si", // 去掉 注释标记
            "'([rn])[s]+'", // 去掉空白字符
            "'&(quot|#34);'i", // 替换 HTML 实体
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(d+);'" // 作为PHP代码运行
        ];

        $replace = [
            "",
            "",
            "",
            "",
            "\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\1)"
        ];

        $str = preg_replace($search, $replace, $str);
        $str = self::removeSpace($str);
        $str = mb_convert_encoding($str, 'UTF-8');

        return trim($str);
    }


    /**
     * 移除HTML标签(保留行内空格)
     * @param string $html
     * @return string
     */
    public static function removeHtml(string $html): string {
        if ($html == '') {
            return '';
        }

        $str = preg_replace("@<(.*?)>@is", "", $html); //过滤标签
        $str = preg_replace("/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i", "", $str); //过滤img标签
        $str = preg_replace("@<style(.*?)<\/style>@is", "", $str); //过滤css
        $str = preg_replace("/\s+/", " ", $str); //过滤多余回车
        $str = preg_replace("/<[ ]+/si", "<", $str); //过滤<__("<"号后面带空格)
        $str = preg_replace("/<\!--.*?-->/si", "", $str); //注释
        $str = preg_replace("/<(\!.*?)>/si", "", $str); //过滤DOCTYPE
        $str = preg_replace("/<(\/?html.*?)>/si", "", $str); //过滤html标签
        $str = preg_replace("/<(\/?head.*?)>/si", "", $str); //过滤head标签
        $str = preg_replace("/<(\/?meta.*?)>/si", "", $str); //过滤meta标签
        $str = preg_replace("/<(\/?body.*?)>/si", "", $str); //过滤body标签
        $str = preg_replace("/<(\/?link.*?)>/si", "", $str); //过滤link标签
        $str = preg_replace("/<(\/?form.*?)>/si", "", $str); //过滤form标签
        $str = preg_replace("/cookie/si", "COOKIE", $str); //过滤COOKIE标签
        $str = preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si", "", $str); //过滤applet标签
        $str = preg_replace("/<(\/?applet.*?)>/si", "", $str); //过滤applet标签
        $str = preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si", "", $str); //过滤style标签
        $str = preg_replace("/<(\/?style.*?)>/si", "", $str); //过滤style标签
        $str = preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si", "", $str); //过滤title标签
        $str = preg_replace("/<(\/?title.*?)>/si", "", $str); //过滤title标签
        $str = preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si", "", $str); //过滤object标签
        $str = preg_replace("/<(\/?objec.*?)>/si", "", $str); //过滤object标签
        $str = preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si", "", $str); //过滤noframes标签
        $str = preg_replace("/<(\/?noframes.*?)>/si", "", $str); //过滤noframes标签
        $str = preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si", "", $str); //过滤frame标签
        $str = preg_replace("/<(\/?i?frame.*?)>/si", "", $str); //过滤frame标签
        $str = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $str); //过滤script标签
        $str = preg_replace("/<(\/?script.*?)>/si", "", $str); //过滤script标签
        $str = preg_replace("/javascript/si", "Javascript", $str); //过滤script标签
        $str = preg_replace("/vbscript/si", "Vbscript", $str); //过滤script标签
        $str = preg_replace("/on([a-z]+)\s*=/si", "On\\1=", $str); //过滤script标签
        $str = preg_replace("/&#/si", "&＃", $str); //过滤script标签

        return trim($str);
    }


    /**
     * 字符串/单词统计
     * @param string $str
     * @param int $type 统计类型: 0:按字符统计; 1:只统计英文单词; 2:按英文单词和中文字数
     * @return int
     */
    public static function stringWordCount(string $str, int $type = 0): int {
        $str = trim($str);
        switch ($type) {
            case 0:
            default:
                $len = mb_strlen(self::removeHtml(self::removeSpace($str)), 'UTF-8');
                break;
            case 1:
                $len = str_word_count(self::removeHtml(html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
                break;
            case 2:
                $str         = self::removeHtml(html_entity_decode($str, ENT_QUOTES, 'UTF-8'));
                $utf8_cn     = "/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u";//中文
                $utf8_symbol = "/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u";//中文标点符号

                $str   = preg_replace($utf8_symbol, ' ', $str);
                $cnLen = preg_match_all($utf8_cn, $str, $textrr);

                $str   = preg_replace($utf8_cn, ' ', $str);
                $enLen = str_word_count($str);

                $len = intval($cnLen) + $enLen;
                break;
        }

        return $len;
    }


    /**
     * 隐藏证件号码
     * @param string $str
     * @return string
     */
    public static function hideCard(string $str): string {
        $res = '******';
        $len = strlen($str);
        if ($len > 4 && $len <= 10) {
            $res = substr($str, 0, 4) . '******';
        } elseif ($len > 10) {
            $res = substr($str, 0, 4) . '******' . substr($str, ($len - 4), $len);
        }

        return $res;
    }


    /**
     * 隐藏手机号
     * @param string $str
     * @return string
     */
    public static function hideMobile(string $str): string {
        $res = '***';
        $len = strlen($str);
        if ($len > 7) {
            $res = substr($str, 0, 3) . '****' . substr($str, ($len - 3), $len);
        }

        return $res;
    }


    /**
     * 隐藏真实名称(如姓名、账号、公司等)
     * @param string $str
     * @return string
     */
    public static function hideTrueName(string $str): string {
        $res = '**';
        if ($str != '') {
            $len = mb_strlen($str, 'UTF-8');
            if ($len <= 3) {
                $res = mb_substr($str, 0, 1, 'UTF-8') . $res;
            } elseif ($len < 5) {
                $res = mb_substr($str, 0, 2, 'UTF-8') . $res;
            } elseif ($len < 10) {
                $res = mb_substr($str, 0, 2, 'UTF-8') . '***' . mb_substr($str, ($len - 2), $len, 'UTF-8');
            } elseif ($len < 16) {
                $res = mb_substr($str, 0, 3, 'UTF-8') . '***' . mb_substr($str, ($len - 3), $len, 'UTF-8');
            } else {
                $res = mb_substr($str, 0, 4, 'UTF-8') . '***' . mb_substr($str, ($len - 4), $len, 'UTF-8');
            }
        }

        return $res;
    }


    /**
     * 统计base64字符串大小(字节)
     * @param string $str base64字符串
     * @return int
     */
    public static function countBase64Byte(string $str): int {
        if (empty($str)) {
            return 0;
        }

        $str = preg_replace('/^(data:\s*(image|img)\/(\w+);base64,)/', '', $str);
        $str = str_replace('=', '', $str);
        $len = strlen($str);
        $res = intval($len * (3 / 4));
        return $res;
    }


    /**
     * 将字符串转换成二进制
     * @param string $str
     * @return string
     */
    public static function str2Bin(string $str): string {
        //列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //unpack字符
        foreach ($arr as &$v) {
            $temp = unpack('H*', $v);
            $v    = base_convert($temp[1], 16, 2);
            unset($temp);
        }

        return join(' ', $arr);
    }


    /**
     * 将二进制转换成字符串
     * @param string $str
     * @return string
     */
    public static function bin2Str(string $str): string {
        $arr = explode(' ', $str);
        foreach ($arr as &$v) {
            $v = pack("H" . strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
        }

        return join('', $arr);
    }


    /**
     * 多分隔符切割字符串
     * @param string $str 源字符串
     * @param string ...$delimiters 分隔符数组
     * @return array
     */
    public static function multiExplode(string $str, string ...$delimiters): array {
        $res = [];
        if ($str == '') {
            return $res;
        }

        $dLen = count($delimiters);
        if ($dLen == 0) {
            array_push($res, $str);
        } else {
            if ($dLen > 1) {
                $str = str_replace($delimiters, $delimiters[0], $str);
            }

            $res = explode($delimiters[0], $str);
        }

        return $res;
    }


}