<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/20
 * Time: 14:21
 * Desc: 文件助手类
 */

namespace Kph\Helpers;

class FileHelper {


    /**
     * 获取文件扩展名
     * @param string $file 文件路径
     * @return string
     */
    public static function getFileExt(string $file):string {
        if(strpos($file, '?')) {
            $file = substr($file, 0, strpos($file, '?'));
        }
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }



}