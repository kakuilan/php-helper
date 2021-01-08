<?php
/**
 * Copyright (c) 2020 LKK All rights reserved
 * User: kakuilan
 * Date: 2020/2/12
 * Time: 10:16
 * Desc: 常量
 */


namespace Kph;


/**
 * Class Consts
 * @package Kph
 */
class Consts {


    //整数相关
    const INT_3B_MAX  = 255; //3位最大整数值,tinyint(3)
    const INT_5B_MAX  = 65535; //5位最大整数值,smallint(5)
    const INT_8B_MAX  = 16777215; //8位最大整数值,mediumint(8)
    const INT_10B_MAX = 4294967295; //10位最大整数值,int(10)


    //有效期时间
    const TTL_LONG       = 0; //永久
    const TTL_DEFAULT    = 1800; //默认时间30分钟
    const TTL_ONE_MONTH  = 2592000; //时间1月
    const TTL_HALF_MONTH = 1296000; //时间0.5月
    const TTL_ONE_WEEK   = 604800; //时间1周
    const TTL_ONE_DAY    = 86400; //时间1天
    const TTL_HALF_DAY   = 43200; //时间0.5天
    const TTL_ONE_HOUR   = 3600; //时间1小时
    const TTL_HALF_HOUR  = 1800; //时间半小时
    const TTL_FIF_MINUTE = 900; //时间15分钟
    const TTL_TEN_MINUTE = 600; //时间10分钟
    const TTL_FIV_MINUTE = 300; //时间5分钟
    const TTL_TWO_MINUTE = 120; //时间2分钟
    const TTL_ONE_MINUTE = 60; //时间1分钟


    const UNKNOWN              = 'Unknown'; //未知字符串
    const DELIMITER            = '$@#KSYS#@$'; //本库自定义分隔符
    const PAAMAYIM_NEKUDOTAYIM = '::'; //范围解析操作符-双冒号
    const DYNAMIC_KEY_LEN      = 8; //authcode动态密钥长度,须<32


}