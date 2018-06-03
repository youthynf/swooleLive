<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 15:59
 */

$process = new swoole_process(function(swoole_process $pro){
    // todo
//    echo "第二个参数设置为true时，子进程输出内容不会输出到屏幕上".PHP_EOL;
//    $pro->exec("/usr/local/php/bin/php",[__DIR__.'/test.php']);//执行一个外部的程序,第一个参数为执行脚本命令，第二个参数为文件名
}, true);

$pid = $process->start();
echo $pid . PHP_EOL;