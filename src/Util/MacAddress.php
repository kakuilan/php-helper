<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/12/8
 * Time: 14:35
 * Desc: 物理地址类
 */

namespace Kph\Util;

use Kph\Helpers\OsHelper;
use Kph\Helpers\RegularHelper;
use Kph\Helpers\StringHelper;

/**
 * Class MacAddress
 * @package Kph\Util
 */
class MacAddress {


    /**
     * 命令输出
     * @var string
     */
    private static $output = [];


    /**
     * 地址数组
     * @var array
     */
    private static $addrs = [];


    /**
     * 检查物理地址
     */
    private static function checkAddress(): void {
        if (empty(self::$addrs)) {
            if (OsHelper::isLinux()) {
                self::checkLinux();
            } elseif (OsHelper::isWindows()) {
                self::checkWindows();
            }

            $arr  = array_reverse(self::$output);
            $itfc = '';
            $addr = '';
            foreach ($arr as $item) {
                //物理地址
                if (preg_match(RegularHelper::$patternMacAddress, $item, $match)) {
                    $addr = strtoupper($match[0]);
                }
                //网卡接口
                if (StringHelper::trim(mb_substr($item, 0, 1)) != '') {
                    $tmp  = explode(':', $item);
                    $itfc = StringHelper::trim($tmp[0] ?? $item);
                    if (!empty($addr)) {
                        self::$addrs[$itfc] = $addr;
                        $itfc               = '';
                        $addr               = '';
                    }
                }
            }
        }
    }


    /**
     * linux下检查
     */
    private static function checkLinux(): void {
        self::$output = (array)OsHelper::runCommand("ifconfig -a");
    }


    /**
     * windows下检查
     */
    private static function checkWindows(): void {
        self::$output = (array)OsHelper::runCommand("ipconfig /all");
        if (empty(self::$output)) {
            $exes = [
                $_SERVER['windir'] . "\system\ipconfig.exe",
                $_SERVER['windir'] . "\system32\ipconfig.exe",
            ];
            foreach ($exes as $exe) {
                if (is_file($exe)) {
                    $command      = "{$exe} /all";
                    self::$output = (array)OsHelper::runCommand($command);
                    break;
                }
            }
        }
    }


    /**
     * 获取物理地址
     * @param string $interface 网卡接口名,如eth0
     * @return string
     */
    public static function getAddress(string $interface = ''): string {
        $res = '';
        self::checkAddress();

        if (!empty(self::$addrs)) {
            $res = empty($interface) ? current(self::$addrs) : (self::$addrs[$interface] ?? '');
        }

        return $res;
    }


}