<?php
/**
 * Created by PhpStorm.
 * User: kakuilan@163.com
 * Date: 2020/11/23
 * Time: 10:15
 * Desc: 类型转换助手类
 */

namespace Kph\Helpers;

use Kph\Consts;
use Error;
use Exception;
use Throwable;

/**
 * Class ConvertHelper
 * @package Kph\Helpers
 */
class ConvertHelper {

    /**
     * 对象转数组
     * @param mixed $val
     * @return array
     */
    public static function object2Array($val): array {
        $arr = is_object($val) ? get_object_vars($val) : $val;
        if (is_array($arr)) {
            foreach ($arr as $k => $item) {
                if (is_array($item) && !empty($item)) {
                    $arr[$k] = array_map(__METHOD__, $item);
                } elseif (is_object($item)) {
                    $arr[$k] = self::object2Array($item);
                }
            }
        } else {
            $arr = (array)$arr;
        }

        return $arr;
    }


    /**
     * 数组转对象
     * @param array $arr
     * @return object
     */
    public static function array2Object(array $arr): object {
        foreach ($arr as $k => $item) {
            if (is_array($item)) {
                $arr[$k] = empty($item) ? new \stdClass() : call_user_func(__METHOD__, $item);
            }
        }

        return (object)$arr;
    }


    /**
     * 字符串转十六进制
     * @param string $str
     * @return string
     */
    public static function str2hex(string $str): string {
        $res = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $val = dechex(ord($str[$i]));
            if (strlen($val) < 2) {
                $val = "0" . $val;
            }
            $res .= $val;
        }

        return $res;
    }


    /**
     * 十六进制转字符串
     * @param string $str
     * @return string
     */
    public static function hex2Str(string $str): string {
        $res = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $item = substr($str, $i, 2);
            $item = hexdec($item);
            $val  = chr($item);
            $res  .= $val;
        }

        return $res;
    }




}