<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/12
 * Time: 10:22
 * Desc: 数组助手类
 */


namespace Kph\Helpers;


/**
 * Class ArrayHelper
 * @package Kph\Helpers
 */
class ArrayHelper {


    /**
     * 检查字符串 $str 是否包含数组$arr的元素之一
     * @param string $str
     * @param array $arr 字符串数组
     * @param bool $returnValue 是否返回匹配的值
     * @param bool $case 是否检查大小写
     * @return bool|mixed
     */
    public static function dstrpos(string $str, array $arr, bool $returnValue = false, bool $case = false) {
        if (empty($str) || empty($arr)) {
            return false;
        }

        foreach ($arr as $v) {
            $v = strval($v);
            if ($case ? strpos($str, $v) !== false : stripos($str, $v) !== false) {
                $return = $returnValue ? $v : true;
                return $return;
            }
        }

        return false;
    }


    /**
     * 对多维数组进行排序
     * @param array $arr 多维数组
     * @param string $sortKey 排序键值
     * @param int $sort 排序类型:SORT_DESC/SORT_ASC
     * @return array
     */
    public static function multiArraySort(array $arr, string $sortKey, int $sort = SORT_DESC): array {
        $keyArr = [];
        foreach ($arr as $subArr) {
            if (!is_array($subArr) || !isset($subArr[$sortKey])) {
                return [];
            }
            array_push($keyArr, $subArr[$sortKey]);
        }
        array_multisort($keyArr, $sort, $arr);

        return $arr;
    }


    /**
     * 多维数组去重
     * @param array $arr
     * @param bool $keepKey 是否保留键值
     * @return array
     */
    public static function multiArrayUnique(array $arr = [], bool $keepKey = false): array {
        $hasArr = $newArr = [];
        foreach ($arr as $k => $v) {
            $hash = md5(serialize($v));
            if (!in_array($hash, $hasArr)) {
                array_push($hasArr, $hash);
                if ($keepKey) {
                    $newArr[$k] = $v;
                } else {
                    $newArr[] = $v;
                }
            }
        }
        unset($hasArr);

        return $newArr;
    }


}