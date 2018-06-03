<?php
namespace app\index\controller;
use app\common\lib\ali\Sms;

class Index
{
    public function index()
    {
        return '';
    }

    public function singwa()
    {
        return date("Y-m-d H:i:s");
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function sms()
    {
        try {
//            发送验证吗
//            Sms::sendSms('18819259295', 12345);
            return "发送成功";
        } catch (\Exception $e) {

        }
    }
}
