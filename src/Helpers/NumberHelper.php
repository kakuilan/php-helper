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



}