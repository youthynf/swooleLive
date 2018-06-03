<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 13:40
 */
namespace app\common\lib;

class Redis
{
    //验证码redis key前缀
    public static $pre = 'sms_';

    public static function smsKey($phone) {
        return self::$pre.$phone;
    }
}