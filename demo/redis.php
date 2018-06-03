<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 15:27
 */

$redisClient = new swoole_redis;
$redisClient->connect('127.0.0.1', 6379, function(swoole_redis $redisClient, $result){
    echo "connect".PHP_EOL;
    var_dump($result);
//    $redisClient->set('singwa', time(), function(swoole_redis $redisClient, $result){
//        var_dump($result);
//    });
//    $redisClient->get('singwa', function (swoole_redis $redisClient, $result){
//        var_dump($result);
//        $redisClient->close();
//    });
    $redisClient->keys('*', function (swoole_redis $redisClient, $result) {
       var_dump($result);
       $redisClient->close();
    });
});

echo "after".PHP_EOL;
