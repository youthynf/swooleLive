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

class Send
{
    //发送验证码
    public function index()
    {
        //tp input
        $phoneNum = request()->get('phone_num',0, 'intval');
//        var_dump($phoneNum);
        if(empty($phoneNum)) {
            //status 0 1 message data
            return Util::show(config('code.error'),'error');
        }
        //生成一个随机数作为验证码
        $code = rand(1000, 9999);

        $taskData = [
            'method' => 'sendSms',
            'data' =>[
                'phone' => $phoneNum,
                'code' => $code,
            ]
        ];
        $_POST['http_server']->task($taskData);

        //采用swoole的task进行异步处理
        /*
        try {
//            发送验证码
//            $response = Sms::sendSms($phoneNum, $code);
            $response = new class {
                public $Code = "OK";
            };
        } catch (\Exception $e) {
            return Util::show(config('code.error','阿里大于内部异常'));
        }
        */
//        if($response->Code === "OK"){
            //同步代码 异步redis
//            $redis = new \Swoole\Coroutine\Redis();
//            $redis->connect(config('redis.host'), config('redis.port'));
//            $redis->set(Redis::smsKey($phoneNum), $code, config('redis.out_time'));
            return Util::show(config('code.success', '验证码发送成功'), ['phone'=>$phoneNum, 'code'=>$code]);
//        } else {
//            return Util::show(config('code.error', '验证码发送失败'));
//        }

        //记录验证码到redis

    }
}
