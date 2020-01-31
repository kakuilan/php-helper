<?php
/**
 * Copyright (c) 2019 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/12/23
 * Time: 18:43
 * Desc:
 */

namespace Kph\Tests\Objects;

use Kph\Services\BaseService;

class BaseServ extends BaseService {


    public static function sum($a, $b) {
        return intval($a) + intval($b);
    }


    public static function value($value) {
        return $value;
    }


    public static function concat($value, $key) {
        return strval($value) . ':' . strval($key);
    }


    public static function join($value, $key, $array) {
        $res = [];
        array_push($res, $value, $key, $array);

        return json_encode($res);
    }


    public static function multiParams($a, $b, $c, $d, $e, $f, $g) {
        $arr = [$a, $b, $c, $d, $e, $f, $g];
        $res = array_reduce($arr, [self::class, 'sum', 0]);
        return $res;
    }


}