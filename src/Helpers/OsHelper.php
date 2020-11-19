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
use Error;
use Exception;
use Throwable;

/**
 * Class OsHelper
 * @package Kph\Helpers
 */
class OsHelper {


    /**
     * 获取操作系统名称
     * @return string
     */
    public static function getOS(): string {
        return PHP_OS;
    }


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
        return strtolower(PHP_OS) == 'linux';
    }


    /**
     * 是否MAC系统
     * @return bool
     */
    public static function isMac(): bool {
        return stripos(PHP_OS, 'Darwin') !== false;
    }


    /**
     * 是否cli模式
     * @return bool
     */
    public static function isCliMode(): bool {
        return PHP_SAPI === 'cli';
    }


    /**
     * 获取PHP路径
     * @return string
     */
    public static function getPhpPath(): string {
        $res   = '';
        $paths = explode(PATH_SEPARATOR, getenv('PATH'));
        foreach ($paths as $path) {
            $file = $path . DIRECTORY_SEPARATOR . 'php';
            if (file_exists($file) && is_executable($file)) {
                $res = $file;
                break;
            }
        }
        return $res;
    }


    /**
     * 检查主机端口是否开放
     * @param string $host 主机/IP
     * @param int $port 端口
     * @param int $timeout 超时,秒
     * @return bool
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
    public static function isWritable(string $path): bool {
        return file_exists($path) && is_writable($path);
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
        $ub       = '';
        $version  = '';

        //First get the platform
        if (preg_match('/iPad/i', $userAgent)) {
            $platform = 'iPad';
        } elseif (preg_match('/iPod/i', $userAgent)) {
            $platform = 'iPod';
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            $platform = 'iPhone';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'MAC';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/unix/i', $userAgent)) {
            $platform = 'Unix';
        } elseif (preg_match('/bsd/i', $userAgent)) {
            $platform = 'BSD';
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
        } elseif (preg_match('/Maxthon/i', $userAgent)) {
            $bname = 'Maxthon';
            $ub    = "Maxthon";
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $bname = 'Google Chrome';
            $ub    = "Chrome";
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $bname = 'Apple Safari';
            $ub    = "Safari";
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $bname = 'Opera';
            $ub    = "Opera";
        } elseif (preg_match('/Lynx/i', $userAgent)) {
            $bname = 'Lynx';
            $ub    = "Lynx";
        } elseif (preg_match('/w3m/i', $userAgent)) {
            $bname = 'w3m';
            $ub    = "w3m";
        }

        // finally get the correct version number
        $known   = ['Version', $ub, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $userAgent, $matches);

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($userAgent, "Version") < strripos($userAgent, $ub)) {
                $version = $matches['version'][0] ?? '';
            } else {
                $version = $matches['version'][1] ?? '';
            }
        } else {
            $version = $matches['version'][0] ?? '';
        }

        // check if we have a number
        if ($version == null || $version == '') {
            $version = "?";
        }
        $res = [
            'name'     => $bname,        //浏览器名称
            'version'  => $version,    //浏览器版本
            'platform' => $platform,    //使用平台
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
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/mac/i', $userAgent)) {
            $os = 'MAC';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/unix/i', $userAgent)) {
            $os = 'Unix';
        } elseif (preg_match('/bsd/i', $userAgent)) {
            $os = 'BSD';
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

        $ip = '';
        if (!empty($server)) {
            //获取代理ip
            if (isset($server["HTTP_X_FORWARDED_FOR"]) && preg_match_all('#(\d+\.){3}\d+#', $server['HTTP_X_FORWARDED_FOR'], $matches)) {
                foreach ($matches[0] as $xip) {
                    $ip = $xip;
                    if (!preg_match('/^(10|172\.16|192\.168)\./', $xip)) {
                        break;
                    }
                }
            } else {
                $ip = $server["HTTP_CLIENT_IP"] ?? ($server["REMOTE_ADDR"] ?? '');
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
    public static function getServerIP(array $server = null): string {
        if (is_null($server)) {
            $server = $_SERVER;
        }

        if (!empty($server)) {
            $serverIp = $server['SERVER_ADDR'] ?? ($server['LOCAL_ADDR'] ?? '');
        } elseif (getenv('SERVER_ADDR')) {
            $serverIp = getenv('SERVER_ADDR');
        }

        if (!isset($serverIp) || empty($serverIp)) {
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
        $domain = '';
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
        $relateUrl = $server['REQUEST_URI'] ?? ltrim($phpSelf, '/') . (isset($server['QUERY_STRING']) ? '?' . $server['QUERY_STRING'] : $pathInfo);
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

        $uri = ($server['PHP_SELF'] ?? '') . "?" . ($server['QUERY_STRING'] ?? ($server['argv'][0] ?? ''));
        return $uri;
    }


    /**
     * IP地址转成无符号整型(内置函数ip2long会返回负值)
     * @param string $ip
     * @return false|int|string
     */
    public static function ip2UnsignedInt(string $ip) {
        if (empty($ip)) {
            return 0;
        }

        $long = ip2long($ip);
        if ($long == false) {
            $long = 0;
        } else {
            $long = sprintf('%u', $long);
        }

        return $long;
    }


    /**
     * 获取远程图片宽高和大小.获取失败,返回空数组;否则返回非空数组.
     * @param string $url 图片地址
     * @param string $type 获取方式:curl或fread
     * @param bool $isGetFilesize 是否获取远程图片的体积大小, 默认false不获取, 设置为 true 时 $type 将强制为 fread
     * @param int $timeout 超时,秒
     * @param int $length 读取长度
     * @param int $trys 最多尝试次数
     * @param null $handle 文件句柄
     * @return array
     */
    public static function getRemoteImageSize(string $url, string $type = 'curl', bool $isGetFilesize = false, int $timeout = 2, int $length = 168, int $trys = 3, $handle = null): array {
        if (!in_array($type, ['curl', 'fread'])) {
            $type = 'curl';
        }

        // 若需要获取图片体积大小则默认使用 fread 方式
        if ($isGetFilesize) {
            $type = 'fread';
        }
        $handle = ($type == 'fread' && empty($handle)) ? @fopen($url, 'rb') : null;
        $res    = [];

        if (!is_null($handle) && is_resource($handle)) {
            // 或者使用 socket 二进制方式读取, 需要获取图片体积大小最好使用此方法
            // 只取头部固定长度168字节数据
            $dataBlock = fread($handle, $length);
        } else {
            // 据说 CURL 能缓存DNS 效率比 socket 高
            $ch = curl_init($url);
            // 超时设置
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            // 取前面 168 个字符 通过四张测试图读取宽高结果都没有问题,若获取不到数据可适当加大数值
            curl_setopt($ch, CURLOPT_RANGE, "0-{$length}");
            // 跟踪301跳转
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            // 返回结果
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $dataBlock = curl_exec($ch);
            curl_close($ch);
        }
        if (empty($dataBlock)) {
            return $res;
        }

        // 将读取的图片信息转化为图片路径并获取图片信息,经测试,这里的转化设置 jpeg 对获取png,gif的信息没有影响,无须分别设置
        // 有些图片虽然可以在浏览器查看但实际已被损坏可能无法解析信息
        $str64 = base64_encode($dataBlock);
        $size  = getimagesize('data:image/jpeg;base64,' . $str64);
        if (empty($size)) {
            if ($trys > 0) {
                $res = self::getRemoteImageSize($url, $type, $isGetFilesize, $timeout, $length * 10, ($trys - 1), $handle);
                return $res;
            }
            return [];
        }

        $res['width']  = $size[0];
        $res['height'] = $size[1];
        $res['size']   = 0;

        // 是否获取图片体积大小
        if ($isGetFilesize) {
            // 获取文件数据流信息
            $meta = stream_get_meta_data($handle);
            // nginx 的信息保存在 headers 里，apache 则直接在 wrapper_data
            $dataInfo = isset($meta['wrapper_data']['headers']) ? $meta['wrapper_data']['headers'] : $meta['wrapper_data'];
            foreach ($dataInfo as $va) {
                if (preg_match('/length/iU', $va)) {
                    $ts          = explode(':', $va);
                    $res['size'] = trim(array_pop($ts));
                    break;
                }
            }
        }

        if ($type == 'fread' && $handle) {
            @fclose($handle);
        }

        return $res;
    }


    /**
     * curl下载
     * @param string $url URL地址
     * @param string $savefile 保存路径
     * @param array $param 其他参数:timeout,connect_timeout
     * @param bool $returnContent 是否返回下载文件的内容
     * @return bool|string
     */
    public static function curlDownload(string $url, string $savefile = '', array $param = [], bool $returnContent = false) {
        $res = $returnContent ? '' : false;
        if (empty($url) || !ValidateHelper::isUrl($url)) {
            return $res;
        }

        $tempFile = tempnam(sys_get_temp_dir(), uniqid(date('ymd-His'), true));
        if (empty($savefile)) {
            $savefile = $tempFile;
        } elseif (file_exists($savefile)) {
            $res = $returnContent ? file_get_contents($savefile) : true;
            return $res;
        }

        $fp = @fopen($savefile, 'w+');
        if ($fp === false) {
            return $res;
        }

        $timeout     = intval($param['timeout'] ?? 5);
        $conntimeout = intval($param['connect_timeout'] ?? 1);

        //Create a cURL handle.
        $ch = curl_init($url);

        //Pass our file handle to cURL.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_COOKIEJAR, $tempFile . "-cookie");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $tempFile . "-cookie");
        curl_setopt($ch, CURLOPT_FILE, $fp);

        //Timeout if the file doesn't download after N seconds.
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $conntimeout);

        //Execute the request.
        curl_exec($ch);

        if (curl_errno($ch)) {
            return $res;
        }

        //Get the HTTP status code.
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Close the cURL handler.
        @curl_close($ch);

        //Close the file handler.
        @fclose($fp);

        if ($statusCode == 200) {
            $res = $returnContent ? file_get_contents($savefile) : true;
        }

        return $res;
    }


}