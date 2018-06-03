<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 17:17
 */

$http = new swoole_http_server('0.0.0.0', 8801);

$http->on('request', function($request, $response) {

    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $value = $redis->get($request->get['a']);
    $response->header("Content-Type", "text/plain");
    $response->end($value);
});

$http->start();