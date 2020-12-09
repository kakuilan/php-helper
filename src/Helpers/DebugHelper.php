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

    const ERROR_TYPES = [
        '1'     => '致命运行时错误(E_ERROR)',
        '2'     => '运行时警告(E_WARNING)',
        '4'     => '编译时语法解析错误(E_PARSE)',
        '8'     => '运行时提示(E_NOTICE)',
        '16'    => 'PHP初始化致命错误(E_CORE_ERROR)',
        '32'    => 'PHP初始化警告(E_CORE_WARNING)',
        '64'    => 'Zend致命编译时错误(E_COMPILE_ERROR)',
        '128'   => 'Zend编译时警告(E_COMPILE_WARNING)',
        '256'   => '用户产生的错误(E_USER_ERROR)',
        '512'   => '用户产生的警告(E_USER_WARNING)',
        '1024'  => '用户产生的提示(E_USER_NOTICE)',
        '2048'  => '代码提示(E_STRICT)',
        '4096'  => '可捕获的致命错误(E_RECOVERABLE_ERROR)',
        '8192'  => '运行时提示(E_DEPRECATED)',
        '16384' => '用户警告信息(E_USER_DEPRECATED)',
        '30719' => '所有错误警告(E_ALL)',
    ];


    /**
     * 错误日志捕获
     * @param string $logFile
     */
    public static function errorLogHandler(string $logFile = ''): void {
        if (empty($logFile)) {
            $tmpDir  = sys_get_temp_dir();
            $logFile = $tmpDir . '/phperr_' . date('Ymd') . '.log';
        }

        ini_set('log_errors', 1); //设置错误信息输出到文件
        ini_set('ignore_repeated_errors', 1);//不重复记录出现在同一个文件中的同一行代码上的错误信息

        $error = error_get_last();//获取最后发生的错误
        if (is_array($error)) {
            $errorType = self::ERROR_TYPES[$error['type']] ?? '未知类型';

            $msg = sprintf('[%s] %s %s %s line:%s',
                date("Y-m-d H:i:s"),
                $errorType,
                $error['message'],
                $error['file'],
                $error['line']);

            //必须显式地记录错误
            error_log($msg . "\r\n", 3, $logFile);
        }
    }


}