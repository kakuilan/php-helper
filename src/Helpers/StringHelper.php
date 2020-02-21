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


    public static function length(string $str, bool $filterTags = false): int {
        if ($filterTags) {

        }
    }


}