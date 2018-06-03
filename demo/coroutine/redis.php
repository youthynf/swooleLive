<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date 2018-06-03
 * Time: 17:59
 */

$http = new swoole_http_server('0.0.0.0', 8001);

$http->on('request', function($request, $response) {
    // 获取redis 里面 的key的内容， 然后输出浏览器

    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $value = $redis->get($request->get['a']);

    $response->header("Content-Type", "text/plain");
    $response->end($value);
});

$http->start();
