<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/17
 * Time: 09:36
 * Desc: 日期助手类
 */

namespace Kph\Helpers;

class DateHelper {


    /**
     * 智能时间格式
     * @param int|string $datetime 时间戳或日期字符串
     * @param string $format 格式化
     * @return string
     */
    public static function smartDatetime($datetime, string $format = 'Y-n-j H:i'): string {
        $time = is_numeric($datetime) ? $datetime : strtotime($datetime);
        $sec  = time() - intval($time);
        $hour = floor($sec / 3600);

        if ($hour == 0) {
            $min = floor($sec / 60);
            if ($min == 0) {
                $res = '刚刚';
            } else {
                $res = $min . '分钟前';
            }
        } elseif ($hour < 24) {
            $res = $hour . '小时前';
        } elseif ($hour < (24 * 30)) {
            $res = intval($hour / 24) . '天前';
        } elseif ($hour < (24 * 30 * 6)) {
            $res = intval($hour / (24 * 30)) . '月前';
        } else {
            $res = date($format, $time);
        }

        return $res;
    }


    /**
     * 获取指定月份的天数
     * @param int $month 月份
     * @param int $year 年份
     * @return int
     */
    public static function getMonthDays(int $month = 0, int $year = 0): int {
        $monthsMap = [1 => 31, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31];

        if ($month <= 0) {
            $month = date('n');
        }

        if ($year <= 0) {
            $year = date('Y');
        }

        if (array_key_exists($month, $monthsMap)) {
            return $monthsMap[$month];
        } elseif ($month > 12) {
            return 0;
        } else {
            if ($year % 100 === 0) {
                if ($year % 400 === 0) {
                    return 29;
                } else {
                    return 28;
                }
            } else if ($year % 4 === 0) {
                return 29;
            } else {
                return 28;
            }
        }
    }


    /**
     * 将秒数转换为时间字符串
     * 如：
     * 10 将转换为 00:10，
     * 120 将转换为 02:00，
     * 3601 将转换为 01:00:01
     * @param int $second
     * @return string
     */
    public static function second2time(int $second = 0): string {
        if ($second <= 0) {
            return '';
        }

        $hours   = floor($second / 3600);
        $hours   = $hours ? str_pad($hours, 2, '0', STR_PAD_LEFT) : 0;
        $second  = $second % 3600;
        $minutes = floor($second / 60);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $seconds = $second % 60;
        $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

        return implode(':', $hours ? compact('hours', 'minutes', 'seconds') : compact('minutes', 'seconds'));
    }


    /**
     * 获取时间戳的微秒部分,单位/微秒.
     * @return float
     */
    public static function getMicrosecond():float {
        list($usec,) = explode(" ", microtime());
        return ((float)$usec * pow(10, 6));
    }


    /**
     * 获取时间戳,单位/毫秒.
     * @return float
     */
    public static function getMillitime():float {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }





}
