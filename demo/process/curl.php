<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 16:47
 */

echo "process-start-time:".date("Ymd H:i:s");

$workers = [];

$url = [
    'http://baidu.com',
    'http://sina.com.cn',
    'http://qq.com',
    'http://baidu.com?search=singwa',
    'http://baidu.com?search=singwa2',
    'http://baidu.com?search=imooc',
];

for($i = 0; $i < 6; $i++) {
//    开启子进程
    $process = new swoole_process(function (swoole_process $worker) use($i, $url){
        //curl
        $content = curlData($url[$i]);
        echo $content.PHP_EOL;//同理写入管道也可以使用：$process->write($content);
    }, true);
    $pid = $process->start();
    $workers[$pid] = $process;
}

//获取管道里面的数据内容
foreach($workers as $process) {
    echo $process->read();
}

function curlData($url) {
    //curl file_get_contents
    sleep(1);
    return $url."success".PHP_EOL;
}

echo "process-end-time:".date("Ymd H:i:s");