<?php

/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/17
 * Time: 09:36
 * Desc: 日期助手类
 */

namespace Kph\Helpers;

use \DateTime;

/**
 * Class DateHelper
 * @package Kph\Helpers
 */
class DateHelper {

    /**
     * 时间转时间戳
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 要转成时间戳的时间,为null或空则默认当前
     * @return int
     */
    public static function timestamp($time = null): int {
        if (is_null($time) || $time === '') {
            $res = time();
        } elseif (is_string($time) && !is_numeric($time)) {
            $res = strtotime(trim($time));
        } elseif ($time instanceof DateTime) {
            $res = $time->getTimestamp();
        } else {
            $res = intval($time);
        }

        return $res;
    }

    /**
     * 格式化时间
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 待格式化的时间
     * @param string $format 格式
     * @return string
     */
    public static function format($time = null, $format = 'Y-m-d H:i:s'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 智能时间格式
     * @param int|string $datetime 时间戳或日期字符串
     * @param string $format 格式化
     * @return string
     */
    public static function smartDatetime($datetime, string $format = 'Y-n-j G:i'): string {
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
    public static function getMicrosecond(): float {
        [$usec,] = explode(" ", microtime());
        return (float)$usec * pow(10, 6);
    }


    /**
     * 获取时间戳,单位/毫秒.
     * @return float
     */
    public static function getMillitime(): float {
        [$t1, $t2] = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


    /**
     * 根据时间获取星座
     * @param int|string $datetime 时间戳或Y-m-d格式日期
     * @return string
     */
    public static function getXingZuo($datetime): string {
        $res = '';
        if (is_numeric($datetime) && strlen($datetime) == 10) {
            $datetime = date('Y-m-d H:i:s', $datetime);
        } else {
            $datetime = strval($datetime);
        }

        if (!ValidateHelper::isDate2time($datetime)) {
            return $res;
        }

        $month = substr($datetime, 5, 2); //取出月份
        $day   = intval(substr($datetime, 8, 2)); //取出日期
        switch ($month) {
            case "01":
                if ($day < 21) {
                    $res = '摩羯';
                } else {
                    $res = '水瓶';
                }
                break;
            case "02":
                if ($day < 20) {
                    $res = '水瓶';
                } else {
                    $res = '双鱼';
                }
                break;
            case "03":
                if ($day < 21) {
                    $res = '双鱼';
                } else {
                    $res = '白羊';
                }
                break;
            case "04":
                if ($day < 20) {
                    $res = '白羊';
                } else {
                    $res = '金牛';
                }
                break;
            case "05":
                if ($day < 21) {
                    $res = '金牛';
                } else {
                    $res = '双子';
                }
                break;
            case "06":
                if ($day < 22) {
                    $res = '双子';
                } else {
                    $res = '巨蟹';
                }
                break;
            case "07":
                if ($day < 23) {
                    $res = '巨蟹';
                } else {
                    $res = '狮子';
                }
                break;
            case "08":
                if ($day < 23) {
                    $res = '狮子';
                } else {
                    $res = '处女';
                }
                break;
            case "09":
                if ($day < 23) {
                    $res = '处女';
                } else {
                    $res = '天秤';
                }
                break;
            case "10":
                if ($day < 24) {
                    $res = '天秤';
                } else {
                    $res = '天蝎';
                }
                break;
            case "11":
                if ($day < 22) {
                    $res = '天蝎';
                } else {
                    $res = '射手';
                }
                break;
            case "12":
                if ($day < 22) {
                    $res = '射手';
                } else {
                    $res = '摩羯';
                }
                break;
        }

        return $res;
    }


    /**
     * 根据时间获取生肖
     * @param int|string $datetime 时间戳或Y-m-d格式日期
     * @return string
     */
    public static function getShengXiao($datetime): string {
        $res = '';
        if (is_numeric($datetime) && strlen($datetime) == 10) {
            $datetime = date('Y-m-d H:i:s', $datetime);
        } else {
            $datetime = strval($datetime);
        }

        if (!ValidateHelper::isDate2time($datetime)) {
            return $res;
        }

        $startYear = 1901;
        $endYear   = intval(substr($datetime, 0, 4));
        $x         = ($startYear - $endYear) % 12;

        switch ($x) {
            case 1:
            case -11:
                $res = "鼠";
                break;
            case 0:
                $res = "牛";
                break;
            case 11:
            case -1:
                $res = "虎";
                break;
            case 10:
            case -2:
                $res = "兔";
                break;
            case 9:
            case -3:
                $res = "龙";
                break;
            case 8:
            case -4:
                $res = "蛇";
                break;
            case 7:
            case -5:
                $res = "马";
                break;
            case 6:
            case -6:
                $res = "羊";
                break;
            case 5:
            case -7:
                $res = "猴";
                break;
            case 4:
            case -8:
                $res = "鸡";
                break;
            case 3:
            case -9:
                $res = "狗";
                break;
            case 2:
            case -10:
                $res = "猪";
                break;
        }

        return $res;
    }


    /**
     * 根据时间获取农历年份(天干地支)
     * @param int|string $datetime 时间戳或Y-m-d格式日期
     * @return string
     */
    public static function getLunarYear($datetime): string {
        $res = '';
        if (is_numeric($datetime) && strlen($datetime) == 10) {
            $datetime = date('Y-m-d H:i:s', $datetime);
        } else {
            $datetime = strval($datetime);
        }

        if (!ValidateHelper::isDate2time($datetime)) {
            return $res;
        }

        //天干
        $sky = ['庚', '辛', '壬', '癸', '甲', '乙', '丙', '丁', '戊', '己'];
        //地支
        $earth = ['申', '酉', '戌', '亥', '子', '丑', '寅', '卯', '辰', '巳', '午', '未'];

        $year = intval(substr($datetime, 0, 4));
        $diff = $year - 1900 + 40;
        $res  = $sky[$diff % 10] . $earth[$diff % 12];
        return $res;
    }


    /**
     * 获取日期中当时的开始时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function startOfHour($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-d H:00:00", $time));
    }


    /**
     * 获取日期中当时的结束时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function endOfHour($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-d H:59:59", $time));
    }


    /**
     * 获取日期中当天的开始时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function startOfDay($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-d", $time));
    }


    /**
     * 获取日期中当天的结束时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function endOfDay($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-d 23:59:59", $time));
    }


    /**
     * 获取日期中当月的开始时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function startOfMonth($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-1", $time));
    }


    /**
     * 获取日期中当月的结束时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function endOfMonth($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-m-t 23:59:59", $time));
    }


    /**
     * 获取日期中当年的开始时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function startOfYear($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-1-1", $time));
    }


    /**
     * 获取日期中当年的结束时间
     * @param DateTime|int|string|null $time 日期/时间
     * @return int
     */
    public static function endOfYear($time = null): int {
        $time = self::timestamp($time);

        return strtotime(date("Y-12-31 23:59:59", $time));
    }


    /**
     * 获取日期中当周的开始时间
     * @param DateTime|int|string|null $time 日期/时间
     * @param int $weekStartDay 周几作为周的第一天;从 1 （表示星期一）到 7 （表示星期日）
     * @return int
     */
    public static function startOfWeek($time = null, int $weekStartDay = 1): int {
        $base       = self::startOfDay($time);
        $curWeekDay = date('w', $base);
        $diff       = $curWeekDay - $weekStartDay;
        if ($diff < 0) {
            $diff += 7;
        }

        return max($base - 86400 * $diff, 0);
    }


    /**
     * 获取日期中当周的结束时间
     * @param DateTime|int|string|null $time 日期/时间
     * @param int $weekStartDay 周几作为周的第一天;从 1 （表示星期一）到 7 （表示星期日）
     * @return int
     */
    public static function endOfWeek($time = null, int $weekStartDay = 1): int {
        $start = self::startOfWeek($time, $weekStartDay);
        return $start + 604799;
    }


    /**
     * 获取年份
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function year($time = null, string $format = 'Y'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 获取月份
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function month($time = null, string $format = 'm'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 获取日
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function day($time = null, string $format = 'd'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 获取时
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function hour($time = null, string $format = 'h'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 获取分钟
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function minute($time = null, string $format = 'i'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 获取秒钟
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 日期/时间
     * @param string $format 格式
     * @return string
     */
    public static function second($time = null, string $format = 's'): string {
        return date($format, self::timestamp($time));
    }

    /**
     * 比较给定时间是否在两个时间之间
     * @Author nece001@163.com
     * @DateTime 2023-04-29
     * @param DateTime|int|string|null $time 给定时间
     * @param DateTime|int|string|null $start 开始时间
     * @param DateTime|int|string|null $end 结束时间
     * @return boolean
     */
    public static function isBetween($time, $start = null, $end = null): bool {
        $time  = self::timestamp($time);
        $start = self::timestamp($start);
        $end   = self::timestamp($end);

        $res = false;
        if ($time != 0 && $start <= $time && $time <= $end) {
            $res = true;
        }

        return $res;
    }
}
