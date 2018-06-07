<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-04
 * Time: 17:49
 */
namespace app\admin\controller;
use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Live
{
    public function Push()
    {
        if(empty($_GET)) {
            return Util::show(config('code.error','error'));
        }
        //token md5(content)
        //查数据库获取，测试
        $teams = [
            '1' => [
                'name' => '马刺',
                'logo' => '/live/imgs/team1.png',
            ],
            '4' => [
                'name' => '火箭',
                'logo' => '/live/imgs/team2.png',
            ],
        ];

        $data = [
            'type' => intval($_GET['type']),
            'time' => !empty($_GET['time']) ? $_GET['time'] : '',
            'title' => !empty($teams[$_GET['team_id']]['name']) ? $teams[$_GET['team_id']]['name'] : '直播员',
            'logo' => !empty($teams[$_GET['team_id']]['logo']) ? $teams[$_GET['team_id']]['logo'] : '',
            'content' => !empty($_GET['content']) ? $_GET['content'] : '',
            'score1' => !empty($_GET['score1']) ? $_GET['score1'] : '',
            'score2' => !empty($_GET['score2']) ? $_GET['score2'] : '',
            'image' => !empty($_GET['image']) ? $_GET['image'] : '',
        ];

        //print_r($_GET);
        //获取连接的用户
        //使用task进行优化
        $taskData = [
            'method' => 'pushLive',
            'data' => $data,
        ];
        $_POST['http_server']->task($taskData);

/*        $clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));
//        print_r($clients);
        foreach($clients as $fd) {
            $_POST['http_server']->push($fd, json_encode($data));
        }*/

        return Util::show(config('code.success'), 'ok');
        //赛况信息入库 推送组装push到直播页面
//        $_POST['http_server']->push(6, 'hello-live-push-data');

    }
}