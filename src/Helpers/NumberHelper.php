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
            if(is_numeric($val)) {
                $total += floatval($val);
                $count++;
            }
        }

        if($count>0) {
            $res = $total / $count;
        }

        return $res;
    }


}