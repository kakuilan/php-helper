<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/24
 * Time: 15:11
 * Desc: 系统或网络助手类
 */

namespace Kph\Helpers;

use Kph\Consts;

/**
 * Class OsHelper
 * @package Kph\Helpers
 */
class OsHelper {


    /**
     * 是否window系统
     * @return bool
     */
    public static function isWindows(): bool {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }


    /**
     * 是否linux系统
     * @return bool
     */
    public static function isLinux(): bool {
        return strtolower(PHP_OS) != 'linux';
    }


    /**
     * 检查主机端口是否开放
     * @param string $host 主机/IP
     * @param int $port 端口
     * @param int $timeout
     * @return bool 超时,秒
     */
    public static function isPortOpen(string $host = '127.0.0.1', int $port = 80, int $timeout = 5): bool {
        $res = false; //端口未绑定
        $fp  = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($errno == 0 && $fp != false) {
            @fclose($fp);
            $res = true; //端口已绑定
        }

        return $res;
    }


    /**
     * 检查文件或目录是否可写
     * @param string $path
     * @return bool
     */
    public static function isReallyWritable(string $path): bool {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR == '/' and ini_get('safe_mode') == false) {
            return is_writable($path);
        }

        // For windows servers and safe_mode "on" installations we'll actually
        // write a file then read it.  Bah...
        if (is_dir($path)) {
            $path = rtrim($path, '/') . '/_isReallyWritable_' . md5(mt_rand(1, 10000));

            if (!file_put_contents($path, 'php isReallyWritable() test file')) {
                return false;
            } else {
                unlink($path);
            }

            return true;
        } elseif (($fp = fopen($path, 'w+')) === false) {
            return false;
        }
        @fclose($fp);

        return true;
    }


    /**
     * 获取浏览器信息数组
     * @param string $userAgent 客户端信息
     * @return array
     */
    public static function getBrowser(string $userAgent = ''): array {
        if (empty($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }

        $bname    = Consts::UNKNOWN;
        $platform = Consts::UNKNOWN;
        $version  = '';

        //First get the platform?
        if (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'MAC';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/unix/i', $userAgent)) {
            $platform = 'Unix';
        } elseif (preg_match('/bsd/i', $userAgent)) {
            $platform = 'BSD';
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            $platform = 'iPhone';
        } elseif (preg_match('/iPad/i', $userAgent)) {
            $platform = 'iPad';
        } elseif (preg_match('/iPod/i', $userAgent)) {
            $platform = 'iPod';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if ((preg_match('/MSIE/i', $userAgent) || strpos($userAgent, 'rv:11.0')) && !preg_match('/Opera/i', $userAgent)) {
            $bname = 'Internet Explorer';
            $ub    = "MSIE";
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $bname = 'Mozilla Firefox';
            $ub    = "Firefox";
        } elseif (preg_match('/Edge/i', $userAgent)) {//win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            $bname = 'Microsoft Edge';
            $ub    = "Edge";
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $bname = 'Google Chrome';
            $ub    = "Chrome";
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $bname = 'Apple Safari';
            $ub    = "Safari";
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $bname = 'Opera';
            $ub    = "Opera";
        } elseif (preg_match('/Netscape/i', $userAgent)) {
            $bname = 'Netscape';
            $ub    = "Netscape";
        } elseif (preg_match('/Maxthon/i', $userAgent)) {
            $bname = 'Maxthon';
            $ub    = "Maxthon";
        } elseif (preg_match('/Lynx/i', $userAgent)) {
            $bname = 'Lynx';
            $ub    = "Lynx";
        } elseif (preg_match('/w3m/i', $userAgent)) {
            $bname = 'w3m';
            $ub    = "w3m";
        }

        // finally get the correct version number
        $known   = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $userAgent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($userAgent, "Version") < strripos($userAgent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == '') {
            $version = "?";
        }
        $res = [
            'userAgent' => $userAgent,    //用户客户端信息
            'name'      => $bname,        //浏览器名称
            'version'   => $version,    //浏览器版本
            'platform'  => $platform,    //使用平台
            'pattern'   => $pattern        //匹配正则
        ];
        return $res;
    }


    /**
     * 获取客户端操作系统
     * @param string $userAgent 客户端信息
     * @return string
     */
    public static function getClientOS(string $userAgent = ''): string {
        if (empty($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }

        $os = Consts::UNKNOWN;
        if (preg_match('/win/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/mac/i', $userAgent)) {
            $os = 'MAC';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/unix/i', $userAgent)) {
            $os = 'Unix';
        } elseif (preg_match('/bsd/i', $userAgent)) {
            $os = 'BSD';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        }

        return $os;
    }


    /**
     * 获取客户端IP
     * @param array $server server信息
     * @return string
     */
    public static function getClientIp(array $server = []): string {
        if (empty($server)) {
            $server = $_SERVER;
        }

        if (!empty($server)) {
            //获取代理ip
            if (isset($server["HTTP_X_FORWARDED_FOR"]) && preg_match_all('#(\d+\.){3}\d+#', $server['HTTP_X_FORWARDED_FOR'], $matches)) {
                foreach ($matches[0] AS $xip) {
                    if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                        $ip = $xip;
                        break;
                    }
                }
            } else if (isset($server["HTTP_CLIENT_IP"])) {
                $ip = $server["HTTP_CLIENT_IP"];
            } else {
                $ip = $server["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else {
                $ip = getenv("REMOTE_ADDR");
            }
        }

        preg_match("/[\d\.]{7,15}/", $ip, $matches);
        $ip = $matches[0] ?? '0.0.0.0';

        return $ip;
    }


    /**
     * 获取服务器IP
     * @param array $server server信息
     * @return string
     */
    public static function getServerIP(array $server = []): string {
        if (empty($server)) {
            $server = $_SERVER;
        }

        if (!empty($server)) {
            if (isset($server['SERVER_ADDR'])) {
                $serverIp = $server['SERVER_ADDR'];
            } else {
                $serverIp = $server['LOCAL_ADDR'];
            }
        } elseif (getenv('SERVER_ADDR')) {
            $serverIp = getenv('SERVER_ADDR');
        } else {
            $serverIp = gethostbyname(gethostname());
        }

        if (!filter_var($serverIp, FILTER_VALIDATE_IP)) {
            $serverIp = '0.0.0.0';
        }

        return $serverIp;
    }


    /**
     * 获取域名
     * @param string $url
     * @param bool $firstLevel 是否获取一级域名,如:abc.test.com取test.com
     * @param array $server server信息
     * @return string
     */
    public static function getDomain(string $url, bool $firstLevel = false, array $server = []): string {
        if (empty($server)) {
            $server = $_SERVER;
        }
        if (empty($url)) {
            $url = $server['HTTP_HOST'] ?? '';
        }

        if (!stripos($url, '://')) {
            $url = 'http://' . $url;
        }

        $parse  = parse_url(strtolower($url));
        $domain = null;
        if (isset($parse['host'])) {
            $domain = $parse['host'];
        }

        if ($firstLevel) {
            $arr  = explode('.', $domain);
            $size = count($arr);
            if ($size >= 2) {
                $domain = $arr[$size - 2] . '.' . end($arr);
            }
        }

        return $domain;
    }


    /**
     * 获取当前页面完整URL地址
     * @param array $server server信息
     * @return string
     */
    public static function getUrl(array $server = []) {
        if (empty($server)) {
            $server = $_SERVER;
        }

        $protocal  = ($server['SERVER_PORT'] ?? '') == '443' ? 'https://' : 'http://';
        $phpSelf   = $server['PHP_SELF'] ?? $server['SCRIPT_NAME'];
        $pathInfo  = $server['PATH_INFO'] ?? '';
        $relateUrl = $server['REQUEST_URI'] ?? $phpSelf . (isset($server['QUERY_STRING']) ? '?' . $server['QUERY_STRING'] : $pathInfo);
        return $protocal . ($server['HTTP_HOST'] ?? '') . $relateUrl;
    }


    /**
     * 获取URI
     * @param array $server
     * @return string
     */
    public static function getUri(array $server = []): string {
        if (empty($server)) {
            $server = $_SERVER;
        }

        if (isset($server['REQUEST_URI'])) {
            return $server['REQUEST_URI'];
        }

        $uri = ($server['PHP_SELF'] ?? '') . "?" . ($server['argv'][0] ?? ($server['QUERY_STRING'] ?? ''));
        return $uri;
    }


}