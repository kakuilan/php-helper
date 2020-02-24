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
     * 字符串剪切(宽字符)
     * @param string $str 字符串
     * @param int $length 截取长度
     * @param int $start 开始位置
     * @param string $dot 省略符
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
                $str .= self::cutStr($chars, floor(mt_rand(0, $charLen - 1)), 1);
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
     * 全角转半角字符
     * @param string $str
     * @return string
     */
    public static function SBC2DBC(string $str): string {
        return str_replace(self::$SBCChars, self::$DBCChars, $str);
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
        preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $matchs);
        $arr = $matchs[0];
        foreach ($arr as $k => $v) {
            $ar[$k] = ord($v[0]) < 128 ? rawurlencode($v) : '%u' . bin2hex(iconv($charset, 'UCS-2', $v));
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
        $str = preg_replace("/\%u([0-9A-Z]{4})/es", "iconv('UCS-2', '$charset', pack('H4', '$1'))", $str);
        return $str;
    }


}