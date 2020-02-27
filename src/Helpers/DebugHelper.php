<?php
/**
 * Copyright (c) 2020 LKK/lanq.net All rights reserved
 * User: kakuilan@163.com
 * Date: 2020/2/24
 * Time: 15:12
 * Desc: 调试助手类
 */

namespace Kph\Helpers;


/**
 * Class DebugHelper
 * @package Kph\Helpers
 */
class DebugHelper {


    /**
     * 错误日志捕获
     * @param string $logFile
     */
    public static function errorLogHandler(string $logFile = ''): void {
        if (empty($logFile)) {
            $logFile = '/tmp/phperr_' . date('Ymd') . '.log';
        }

        ini_set('log_errors', 1); //设置错误信息输出到文件
        ini_set('ignore_repeated_errors', 1);//不重复记录出现在同一个文件中的同一行代码上的错误信息

        $userDefinedErr = error_get_last();//获取最后发生的错误
        if ($userDefinedErr['type'] > 0) {
            switch ($userDefinedErr['type']) {
                case 1:
                    $userDefinedErrType = '致命的运行时错误(E_ERROR)';
                    break;
                case 2:
                    $userDefinedErrType = '非致命的运行时错误(E_WARNING)';
                    break;
                case 4:
                    $userDefinedErrType = '编译时语法解析错误(E_PARSE)';
                    break;
                case 8:
                    $userDefinedErrType = '运行时提示(E_NOTICE)';
                    break;
                case 16:
                    $userDefinedErrType = 'PHP内部错误(E_CORE_ERROR)';
                    break;
                case 32:
                    $userDefinedErrType = 'PHP内部警告(E_CORE_WARNING)';
                    break;
                case 64:
                    $userDefinedErrType = 'Zend脚本引擎内部错误(E_COMPILE_ERROR)';
                    break;
                case 128:
                    $userDefinedErrType = 'Zend脚本引擎内部警告(E_COMPILE_WARNING)';
                    break;
                case 256:
                    $userDefinedErrType = '用户自定义错误(E_USER_ERROR)';
                    break;
                case 512:
                    $userDefinedErrType = '用户自定义警告(E_USER_WARNING)';
                    break;
                case 1024:
                    $userDefinedErrType = '用户自定义提示(E_USER_NOTICE)';
                    break;
                case 2048:
                    $userDefinedErrType = '代码提示(E_STRICT)';
                    break;
                case 4096:
                    $userDefinedErrType = '可以捕获的致命错误(E_RECOVERABLE_ERROR)';
                    break;
                case 8191:
                    $userDefinedErrType = '所有错误警告(E_ALL)';
                    break;
                default:
                    $userDefinedErrType = '未知类型';
                    break;
            }

            $msg = sprintf('[%s] %s %s %s line:%s',
                date("Y-m-d H:i:s"),
                $userDefinedErrType,
                $userDefinedErr['message'],
                $userDefinedErr['file'],
                $userDefinedErr['line']);

            //必须显式地记录错误
            error_log($msg . "\r\n", 3, $logFile);
        }
    }


}