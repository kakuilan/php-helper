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
    public static function multiArrayUnique(array $arr, bool $keepKey = false): array {
        $hasArr = $res = [];
        foreach ($arr as $k => $v) {
            $hash = md5(serialize($v));
            if (!in_array($hash, $hasArr)) {
                array_push($hasArr, $hash);
                if ($keepKey) {
                    $res[$k] = $v;
                } else {
                    $res[] = $v;
                }
            }
        }
        unset($hasArr);

        return $res;
    }


    /**
     * 取多维数组的最底层值
     * @param array $arr
     * @param array $vals 结果
     * @return array
     */
    public static function multiArrayValues(array $arr, &$vals = []): array {
        foreach ($arr as $v) {
            if (is_array($v)) {
                self::multiArrayValues($v, $vals);
            } else {
                array_push($vals, $v);
            }
        }

        return $vals;
    }


    /**
     * 二维数组按指定的键值排序
     * @param array $arr
     * @param string $key 排序的键
     * @param string $sort 排序方式:desc/asc
     * @param bool $keepKey 是否保留键值
     * @return array
     */
    public static function arraySort(array $arr, string $key, string $sort = 'desc', bool $keepKey = false): array {
        $res    = [];
        $values = [];
        $sort   = strtolower(trim($sort));
        foreach ($arr as $k => $v) {
            if (!isset($v[$key])) {
                return [];
            }
            $values[$k] = $v[$key];
        }

        if ($sort === 'asc') {
            asort($values);
        } else {
            arsort($values);
        }
        reset($values);
        foreach ($values as $k => $v) {
            if ($keepKey) {
                $res[$k] = $arr[$k];
            } else {
                $res[] = $arr[$k];
            }
        }

        return $res;
    }


    /**
     * 对数组元素递归求值
     * @param array $arr
     * @param callable $fn 回调函数
     * @return array
     */
    public static function arrayMapRecursive(array $arr, callable $fn): array {
        $res = [];
        foreach ($arr as $k => $v) {
            $res[$k] = is_array($v) ? (self::arrayMapRecursive($v, $fn)) : call_user_func($fn, $v);
        }
        return $res;
    }


    /**
     * 对象转数组
     * @param $val
     * @return array
     */
    public static function object2Array($val): array {
        $arr = is_object($val) ? get_object_vars($val) : $val;
        if (is_array($arr)) {
            return array_map(__METHOD__, $arr);
        }

        return (array)$arr;
    }


    /**
     * 数组转对象
     * @param array $arr
     * @return object
     */
    public static function arrayToObject(array $arr): object {
        return (object)array_map(__METHOD__, $arr);
    }


    /**
     * 数组元素组合
     * @param array $arr 数组
     * @param int $len 组合长度
     * @param string $separator 分隔符
     * @return array
     */
    private static function _combination(array $arr, int $len, string $separator = ''): array {
        $res = [];
        if ($len <= 0) {
            return $res;
        } elseif ($len == 1) {
            return $arr;
        } elseif ($len == count($arr)) {
            array_push($res, implode($separator, $arr));
            return $res;
        }

        $firstItem = array_shift($arr);
        $newArr    = array_values($arr);

        $list1 = self::_combination($newArr, $len - 1, $separator);
        foreach ($list1 as $item) {
            $str = strval($firstItem) . $separator . strval($item);
            array_push($res, $str);
        }

        $list2 = self::_combination($newArr, $len, $separator);
        foreach ($list2 as $item) {
            array_push($res, strval($item));
        }

        return $res;
    }


    /**
     * 以字符串形式,排列组合数组的元素
     * @param array $arr 要排列组合的数组
     * @param string $separator 分隔符
     * @return array
     */
    public static function combination2String(array $arr, $separator = ''): array {
        $res = [];
        $len = count($arr);
        for ($i = 1; $i <= $len; $i++) {
            $news = self::_combination($arr, $i, $separator);
            if (!empty($news)) {
                $res = array_merge($res, $news);
            }
        }

        return $res;
    }


}