<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 18:02
 */
 $http = new swoole_http_server("0.0.0.0", 8811);

 $http->set([
     'enable_static_handler' => true,
     'document_root' => "/usr/local/nginx/html/thinkphp/public/static",
     'worker_num' => 5,
 ]);

 $http->on('WorkerStart', function(swoole_server $server, $worker_id){
     // 定义应用目录
     define('APP_PATH', __DIR__ . '/../application/');
     require __DIR__ . '/../thinkphp/base.php';
 });

 $http->on('request', function($request, $response) use($http){
     $_SERVER = [];
//     print_r($request->server);
     if(isset($request->server)){
         foreach ($request->server as $k => $v) {
             $_SERVER[strtoupper($k)] = $v;
         }
     }
     if(isset($request->header)){
         foreach ($request->header as $k => $v) {
             $_SERVER[strtoupper($k)] = $v;
         }
     }
     $_GET = [];
     if(isset($request->get)){
         foreach ($request->get as $k => $v) {
             $_GET[$k] = $v;
         }
     }
     $_POST = [];
     if(isset($request->post)){
         foreach ($request->post as $k => $v) {
             $_POST[$k] = $v;
         }
     }

     ob_start();
     // 执行应用并响应
     try{
         think\Container::get('app', [APP_PATH ])
             ->run()
             ->send();
     } catch (\Exception $e) {

     }
//     echo "-action-".request()->action().PHP_EOL;
     $res = ob_get_contents();
     ob_end_clean();
     $response->end($res);
//     $http->close();
 });

 $http->start();