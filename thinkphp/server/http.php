<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 20:27
 */
use app\common\lib\ali\Sms;
class Http
{
    CONST HOST = "0.0.0.0";
    CONST PROT = 8811;

    public $http = null;
    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PROT);

        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => "/usr/local/nginx/html/thinkphp/public/static",
            'worker_num' => 4,
            'task_worker_num' => 4,
        ]);

        $this->http->on('workerstart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('task', [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on('close', [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * onworkerStart 回调
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id) {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
//        require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return string
     */
    public function onTask($serv, $taskId, $workerId, $data) {
        //分发 task 任务机制 让不同的任务 走不同的逻辑
        $obj = new app\common\lib\task\Task;
        $method = $data['method'];
        $res = $obj->$method($data['data']);

        /*
        try {
//            发送验证码
//            $response = Sms::sendSms($phoneNum, $code);
            $response = new class {
                public $Code = "OK";
            };
        } catch (\Exception $e) {
//            return Util::show(config('code.error','阿里大于内部异常'));
            echo $e->getMessage();
        }
        */

        return $res;
    }

    /**
     * request 回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response) {
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
        $_POST['http_server'] = $this->http;

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
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    public function onClose() {
        echo "http closed";
    }
}

new Http();