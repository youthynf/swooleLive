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

    /**
     * 用户user pre 前缀
     * @var string
     */
    public static $userPre = "user_";

    /**
     * 存储验证码 redis key
     * @param $phone
     * @return string
     */
    public static function smsKey($phone) {
        return self::$pre.$phone;
    }

    /**
     * 获取user key
     * @param $phone
     * @return string
     */
    public static function userkey($phone) {
        return self::$userPre.$phone;
    }
}