<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 13:05
 */
namespace app\index\controller;
use app\common\lib\Util;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Login
{
    //发送验证码
    public function index()
    {
        //phone code
        $phoneNum = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if(empty($phoneNum) || empty($code)) {
            return Util::show(config('code.error'), 'phone or code is error');
        }
        //redis code
        try {
            $redisCode = Predis::getInstance()->get(Redis::smsKey($phoneNum));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if($redisCode == $code) {
            //记录用户信息 登录状态 写入redis
            $data = [
                'user' => $phoneNum,
                'scrKey' => md5(Redis::userkey($phoneNum)),
                'time' => time(),
                'isLogin' => true,
            ];
            Predis::getInstance()->set(Redis::userkey($phoneNum),$data);

            return Util::show(config('code.success'),'ok', $data);
        } else {
            return Util::show(config('code.error'), 'login error');
        }
        //redis.so 同步redis

    }
}
