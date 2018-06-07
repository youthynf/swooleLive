<?php
/**
 * 监控服务 ws http 8811
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-06
 * Time: 16:52
 */
class Server {
    const PORT = 8811;

    public function port() {
        $shell = " netstat -anp 2>/dev/null | grep ".self::PORT." | grep LISTEN | wc -l";
        $result = shell_exec($shell);
        if($result != 1) {
//            echo date("Ymd H:i:s")."error".PHP_EOL;
            $logs = date("Ymd H:i:s") . " : " . 'ws服务终止运行（error）';
            echo $logs . PHP_EOL;
//            echo '/../../../runtime/log/'.date("Ym")."/".date("d")."error.log";
//            swoole_async_writefile( '/../../../runtime/log/'.date("Ym")."/".date("d")."error.log", $logs.PHP_EOL, function() {
//                // TODO
//            }, FILE_APPEND);
        }
    }
}
swoole_timer_tick(2000, function ($timer_id) {
    (new Server())->port();
});