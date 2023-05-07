<?php

namespace Kph\Helpers;

/**
 * 命令行助手类
 *
 * @Author nece001@163.com
 * @DateTime 2023-05-07
 */
class CliHelper {
    /**
     * 在命令行输出进度条
     *
     * @Author nece001@163.com
     * @DateTime 2023-05-07
     *
     * @param integer $total 总数
     * @param integer $current 当前数量
     *
     * @return void
     */
    public static function progress(int $total, int $current) {
        printf("progress: [%-50s] %d%% Done\r", str_repeat('#', $current / $total * 50), $current / $total * 100);
    }
}
