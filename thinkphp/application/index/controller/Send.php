<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 13:05
 */
namespace app\index\controller;
use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;

class Send
{
    //发送验证码
    public function index()
    {
        //tp input
        $phoneNum = request()->get('phone_num',0, 'intval');
        return json_encode(['phone_num'=>$phoneNum]);
//        var_dump($phoneNum);
        if(empty($phoneNum)) {
            //status 0 1 message data
            return Util::show(config('code.error'),'error');
        }

        //生成一个随机数作为验证码
        $code = rand(1000, 9999);
        try {
//            发送验证码
//            $response = Sms::sendSms($phoneNum, $code);
            $response = new class {
                public $Code = "OK";
            };
        } catch (\Exception $e) {
            return Util::show(config('code.error','阿里大于内部异常'));
        }

        if($response->Code === "OK"){
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'),config('redis.port'));
            $redis->set(Redis::smsKey($phoneNum), $code, config('redis.out_time'));
            return Util::show(config('code.success', '验证码发送成功'));
        } else {
            return Util::show(config('code.error', '验证码发送失败'));
        }

        //记录验证码到redis

    }
}
