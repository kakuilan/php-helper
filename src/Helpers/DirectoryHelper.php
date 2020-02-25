<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/20
 * Time: 15:56
 * Desc: 目录助手类
 */

namespace Kph\Helpers;


/**
 * Class DirectoryHelper
 * @package Kph\Helpers
 */
class DirectoryHelper {


    /**
     * 创建深层目录
     * @param string $dir 路径
     * @param int $mode 权限模式
     * @return bool
     */
    public static function mkdirDeep(string $dir, int $mode = 0766): bool {
        if ($dir == '') {
            return false;
        } elseif (is_dir($dir) && chmod($dir, $mode)) {
            return true;
        } elseif (@mkdir($dir, $mode, true)) {//第三个参数为true即可以创建多级目录
            return true;
        }

        return false;
    }


    /**
     * 遍历路径获取文件树
     * @param string $path 路径
     * @param string $type 获取类型:all-所有,dir-仅目录,file-仅文件
     * @param bool $recursive 是否递归
     * @return array
     */
    public static function getFileTree(string $path, string $type = 'all', bool $recursive = true): array {
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $tree = [];
        // '{.,*}*' 相当于 '.*'(搜索.开头的隐藏文件)和'*'(搜索正常文件)
        foreach (glob($path . '/{.,*}*', GLOB_BRACE) as $single) {
            if (is_dir($single)) {
                $file = str_replace($path . '/', '', $single);
                if ($file == '.' || $file == '..') {
                    continue;
                }

                if ($type != 'file') {
                    $tree[] = $single;
                }

                if ($recursive) {
                    $tree = array_merge($tree, self::getFileTree($single, $type, $recursive));
                }
            } elseif ($type != 'dir') {
                $tree[] = $single;
            }
        }

        return $tree;
    }


    /**
     * 获取目录大小,单位[字节]
     * @param string $path
     * @return int
     */
    public static function getDirSize(string $path): int {
        $size = 0;
        if ($path == '' || !is_dir($path)) {
            return $size;
        }

        $dh = @opendir($path); //比dir($path)快
        while (false != ($file = @readdir($dh))) {
            if ($file != '.' and $file != '..') {
                $fielpath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($fielpath)) {
                    $size += self::getDirSize($fielpath);
                } else {
                    $size += filesize($fielpath);
                }
            }
        }
        @closedir($dh);
        return $size;
    }


    /**
     * 拷贝目录
     * @param string $from 源目录
     * @param string $dest 目标目录
     * @param bool $cover 是否覆盖已存在的文件
     * @return bool
     */
    public static function copyDir(string $from, string $dest, bool $cover = false): bool {
        if (!file_exists($dest) && !mkdir($dest, 0766, true)) {
            return false;
        }

        $dh = opendir($from);
        while (false !== ($fileName = readdir($dh))) {
            if (($fileName != ".") && ($fileName != "..")) {
                $newFile = "$dest/$fileName";
                if (!is_dir("$from/$fileName")) {
                    if (file_exists($newFile) && !$cover) {
                        continue;
                    } elseif (!copy("$from/$fileName", $newFile)) {
                        break;
                    }
                } else {
                    self::copyDir("$from/$fileName", $newFile, $cover);
                }
            }
        }
        closedir($dh);

        return true;
    }


    /**
     * 批量改变目录模式(包括子目录和所属文件)
     * @param string $path 路径
     * @param int $filemode 文件模式
     * @param int $dirmode 目录模式
     */
    public static function chmodBatch(string $path, int $filemode = 0766, int $dirmode = 0766): void {
        if ($path == '') {
            return;
        }

        if (is_dir($path)) {
            if (!chmod($path, $dirmode)) {
                return;
            }
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $fullpath = $path . '/' . $file;
                    self::chmodBatch($fullpath, $filemode, $dirmode);
                }
            }
            closedir($dh);
        } elseif (!is_link($path)) {
            chmod($path, $filemode);
        }
    }


    /**
     * 删除目录(目录下所有文件,包括本目录)
     * @param string $path
     * @return bool
     */
    public static function delDir(string $path): bool {
        if (is_dir($path) && $dh = @opendir($path)) {
            while (false != ($file = @readdir($dh))) {
                if ($file != '.' && $file != '..') {
                    $fielpath = $path . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($fielpath)) {
                        self::delDir($fielpath);
                    } else {
                        @unlink($fielpath);
                    }
                }
            }
            @closedir($dh);
            return @rmdir($path);
        }
        return false;
    }


    /**
     * 清空目录(删除目录下所有文件,仅保留当前目录)
     * @param string $path
     * @return bool
     */
    public static function emptyDir(string $path): bool {
        if (empty($path) || !is_dir($path)) {
            return false;
        }

        $dirs    = [];
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $single => $object) {
            $checkNull = strpos(substr($single, -3), "/."); //检查文件是否 . 或者 ..
            if ($checkNull !== false) {
                continue;
            }

            //先删除文件
            if (is_file($single)) {
                @unlink($single);
            } else {
                $dirs[] = $single;
            }
        }

        //再删除目录
        rsort($dirs);
        foreach ($dirs as $dir) {
            @rmdir($dir);
        }

        unset($objects, $object, $dirs);
        return true;
    }


    /**
     * 格式化路径字符串(路径后面加/)
     * @param string $dir
     * @return string
     */
    public static function formatDir(string $dir): string {
        if ($dir == '') {
            return '';
        }

        $order   = [
            '\\',
            "'",
            '#',
            '=',
            '`',
            '$',
            '%',
            '&',
            ';',
            '|'
        ];
        $replace = [
            '/',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $dir = str_replace($order, $replace, $dir);
        return rtrim(preg_replace(RegularHelper::$patternDoubleSlash, '/', $dir), ' /　') . '/';
    }


}