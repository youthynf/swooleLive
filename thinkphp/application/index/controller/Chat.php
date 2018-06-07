<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-06
 * Time: 12:12
 */
namespace app\index\controller;
use app\common\lib\Util;
class Chat
{
    public function index()
    {
        //登录验证待完成
        if(empty($_POST['game_id'])) {
            return Util::show(config('code.erros'),'error');
        }
        if(empty($_POST['content'])) {
            return Util::show(config('code.erros'),'error');
        }

        $data = [
            'user' => '用户'.rand(0, 2000),
            'content' => $_POST['content'],
        ];

        foreach($_POST['http_server']->ports[1]->connections as $fd) {
            $_POST['http_server']->push($fd, json_encode($data));
        }
//        var_dump($_POST['http_server']);

        return Util::show(config('code.success'), 'ok');
    }
}