<?php
/**
 * 处理所有的task任务
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 21:17
 */
namespace app\common\lib\task;
use app\common\lib\ali\Sms;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Task
{
    public function sendSms($data) {
        try {
//            发送验证码
//            $response = Sms::sendSms($phoneNum, $code);
            $response = new class {
                public $Code = "OK";
            };
        } catch (\Exception $e) {
//            return Util::show(config('code.error','阿里大于内部异常'));
//            echo $e->getMessage();
            return false;
        }
        //如果发送成功则写入redis
        if($response->Code === "OK"){
            Predis::getInstance()->set(Redis::smsKey($data['phone']), $data['code'], config('redis.out_time'));
        } else {
            return Util::show(config('code.error', '验证码发送失败'));
        }
        return true;
    }
}