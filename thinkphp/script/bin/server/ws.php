<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 20:27
 */
use app\common\lib\ali\Sms;
use app\common\lib\redis\Predis;
class Ws
{
    CONST HOST = "0.0.0.0";
    CONST PROT = 8811;
    CONST CHAT_PROT = 8812;

    public $ws = null;
    public function __construct()
    {
        //获取 key 有数据则清空
        $redis_instance = new \Redis();
        $redis_instance->connect('127.0.0.1', 6379, 5);
        $clients = $redis_instance->sMembers('live_game_key');
        if(count($clients) > 0) {
            foreach ($clients as $fq) {
                $redis_instance->sRem('live_game_key', $fq);
            }
        }
        $this->ws = new swoole_websocket_server(self::HOST, self::PROT);
        $this->ws->listen(self::HOST, self::CHAT_PROT, SWOOLE_SOCK_TCP);
        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/usr/local/nginx/html/thinkphp/public/static",
            'worker_num' => 4,
            'task_worker_num' => 4,
        ]);

        $this->ws->on("start", [$this, 'onStart']);
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on('workerstart', [$this, 'onWorkerStart']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on('close', [$this, 'onClose']);

        $this->ws->start();
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        //fq redis [1,2]
        if($request->server['server_port'] == self::PROT){
            \app\common\lib\redis\Predis::getInstance()->sAdd(config('redis.live_game_key'), $request->fd);
        }
//        print_r(config('redis.live_game_key') . '-' .$request->fd);
    }

    public function onStart($server) {
        swoole_set_process_name("live_master");
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
    }

    /**
     * onworkerStart 回调
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id) {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../../../application/');
//        require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../../../thinkphp/start.php';
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
        $res = $obj->$method($data['data'], $serv);

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
        if($request->server['request_uri'] == '/favicon.ico'){
            $response->status(404);
            $response->end();
            return ;
        }
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
        $_FILES = [];
        if(isset($request->files)){
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)){
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        $this->writelog();
        $_POST['http_server'] = $this->ws;

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

    public function onClose($ws, $fd) {
        //remove fq redis [1,2]
        \app\common\lib\redis\Predis::getInstance()->sRem(config('redis.live_game_key'), $fd);
        echo "clientid:{$fd}-closed\n";
    }

    /**
     * 记录请求日志
     */
    public function writelog() {
        $datas = array_merge(['data' => date("Ymd H:i:s")], $_GET, $_POST, $_SERVER);

        $logs = '';
        foreach ($datas as $key => $value) {
            $logs .= $key . ":" . $value . " ";
        }
//        var_dump(APP_PATH . '../runtime/log/'.date("Ym")."/".date("d")."_acess.log");
        swoole_async_writefile(APP_PATH . '../runtime/log/'.date("Ym")."/".date("d")."_acess.log", $logs.PHP_EOL, function() {

        }, FILE_APPEND);
    }
}

new Ws();

