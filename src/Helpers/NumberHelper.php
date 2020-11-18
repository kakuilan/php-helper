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
use Kph\Exceptions\BaseException;

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
     * 数值格式化(会四舍五入)
     * @param float|int|string $number 要格式化的数字
     * @param int $decimals 小数位数
     * @return string
     */
    public static function numberFormat($number, int $decimals = 2): string {
        return number_format(floatval($number), $decimals, '.', '');
    }


    /**
     * 数值截取(不会四舍五入)
     * @param float|int|string $number 要格式化的数字
     * @param int $decimals 小数位数
     * @return float
     */
    public static function numberSub($number, int $decimals = 2): float {
        if ($decimals == 0 && ValidateHelper::isInteger($number)) {
            return floatval($number);
        }

        return intval(floatval($number) * pow(10, $decimals)) / pow(10, $decimals);
    }


    /**
     * 生成随机浮点数
     * @param float $min 小值
     * @param float $max 大值
     * @return float
     */
    public static function randFloat(float $min = 0, float $max = 1): float {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }


    /**
     * 将金额转为大写人民币
     * @param float $num 金额,元(1元 = 1000厘,最大支持千亿)
     * @param int $decimals 精确小数位数(最大支持为3,即厘)
     * @return string
     * @throws BaseException
     */
    public static function money2Yuan(float $num, int $decimals = 0): string {
        //TODO
        $int = intval($num);
        if (strlen($int) > 12) {
            throw new BaseException('The maximum value supports 12 bits!');
        }

        if ($decimals > 0) {
            $decimals = min($decimals, 3);
            $num      = $num * pow(10, $decimals);
        }

        return "";
    }


}