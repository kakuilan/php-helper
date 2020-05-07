<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/24
 * Time: 15:32
 * Desc: 数值助手类
 */


namespace Kph\Helpers;

use Kph\Consts;

/**
 * Class ValidateHelper
 * @package Kph\Helpers
 */
class NumberHelper {

    /**
     * 格式化文件比特大小
     * @param int $size 文件大小(比特)
     * @param int $dec 小数位
     * @param string $delimiter 数字和单位间的分隔符
     * @return string
     */
    public static function formatBytes(int $size, int $dec = 2, string $delimiter = ''): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
        }

        return round($size, $dec) . $delimiter . ($units[$i] ?? Consts::UNKNOWN);
    }


    /**
     * 值是否在某范围内
     * @param int|float $val 值
     * @param int|float $min 小值
     * @param int|float $max 大值
     * @return bool
     */
    public static function inRange($val, $min, $max): bool {
        $val = floatval($val);
        $min = floatval($min);
        $max = floatval($max);
        return $val >= $min && $val <= $max;
    }


    /**
     * 对数列求和,忽略非数值.
     * @param mixed ...$vals
     * @return float
     */
    public static function sum(...$vals): float {
        $res = 0;
        foreach ($vals as $val) {
            if (is_numeric($val)) {
                $res += floatval($val);
            }
        }

        return $res;
    }


    /**
     * 对数列求平均值,忽略非数值.
     * @param mixed ...$vals
     * @return float
     */
    public static function average(...$vals): float {
        $res   = 0;
        $count = 0;
        $total = 0;
        foreach ($vals as $val) {
            if (is_numeric($val)) {
                $total += floatval($val);
                $count++;
            }
        }

        if ($count > 0) {
            $res = $total / $count;
        }

        return $res;
    }


    /**
     * 获取地理距离/米.
     * 参数分别为两点的经度和纬度.lat:-90~90,lng:-180~180.
     * @param float $lng1 起点经度
     * @param float $lat1 起点纬度
     * @param float $lng2 终点经度
     * @param float $lat2 终点纬度
     * @return float
     */
    public static function geoDistance(float $lng1 = 0, float $lat1 = 0, float $lng2 = 0, float $lat2 = 0): float {
        $earthRadius = 6371000.0;
        $lat1        = ($lat1 * pi()) / 180;
        $lng1        = ($lng1 * pi()) / 180;
        $lat2        = ($lat2 * pi()) / 180;
        $lng2        = ($lng2 * pi()) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude  = $lat2 - $lat1;
        $stepOne       = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo       = 2 * asin(min(1, sqrt($stepOne)));
        $res           = $earthRadius * $stepTwo;

        return $res;
    }


    /**
     * 数值格式化
     * @param float|int $number 要格式化的数字
     * @param int $decimals 小数位数
     * @param string $decPoint 小数点
     * @param string $thousandssep 千分位符号
     * @return string
     */
    public static function numberFormat($number, int $decimals = 2, string $decPoint = '.', string $thousandssep = ''): string {
        return number_format($number, $decimals, $decPoint, $thousandssep);
    }


    /**
     * 获取日期中当天的开始时间
     * @param int $time 时间戳
     * @return int
     */
    public static function startOfDay(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-m-d", $time));
    }


    /**
     * 获取日期中当天的结束时间
     * @param int $time 时间戳
     * @return int
     */
    public static function endOfDay(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-m-d 23:59:59", $time));
    }


    /**
     * 获取日期中当月的开始时间
     * @param int $time 时间戳
     * @return int
     */
    public static function startOfMonth(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-m-1", $time));
    }


    /**
     * 获取日期中当月的结束时间
     * @param int $time 时间戳
     * @return int
     */
    public static function endOfMonth(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-m-t 23:59:59", $time));
    }


    /**
     * 获取日期中当年的开始时间
     * @param int $time 时间戳
     * @return int
     */
    public static function startOfYear(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-1-1", $time));
    }


    /**
     * 获取日期中当年的结束时间
     * @param int $time 时间戳
     * @return int
     */
    public static function endOfYear(int $time = 0): int {
        if ($time <= 0) {
            $time = time();
        }

        return strtotime(date("Y-12-31 23:59:59", $time));
    }


    /**
     * 获取日期中当周的开始时间
     * @param int $time 时间戳
     * @param int $weekStartDay 周几作为周的第一天;从 1 （表示星期一）到 7 （表示星期日）
     * @return int
     */
    public static function startOfWeek(int $time = 0, int $weekStartDay = 1): int {
        if ($time <= 0) {
            $time = time();
        }

        $day = date('d', $time) - date('N', $time) + $weekStartDay;
        return mktime(0, 0, 0, date('m'), $day, date('Y'));
    }


    /**
     * 获取日期中当周的结束时间
     * @param int $time 时间戳
     * @param int $weekStartDay 周几作为周的第一天;从 1 （表示星期一）到 7 （表示星期日）
     * @return int
     */
    public static function endOfWeek(int $time = 0, int $weekStartDay = 1): int {
        if ($time <= 0) {
            $time = time();
        }

        $day = date('d', $time) - date('N', $time) + $weekStartDay + 6;
        return mktime(23, 59, 59, date('m'), $day, date('Y'));
    }


}