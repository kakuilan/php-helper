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


}