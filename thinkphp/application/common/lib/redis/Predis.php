<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 18:23
 */
namespace app\common\lib\redis;

class Predis
{
    public $redis = "";
    //定义单例模式变量
    private static $_instance = null;

    private function __construct()
    {
        $this->redis = new \Redis();
        $result = $this->redis->connect(config('redis.host'), config('redis.port'),config('redis.timeOut'));
        if($result === false) {
            throw new \Exception(('redis connect error'));
        }
    }

    /**
     * @return Predis|null
     */
    public static function getInstance() {
        if(empty(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     */
    public function set($key, $value, $time = 0) {
        if(!$key) {
            return '';
        }
        if(is_array($value)) {
            $value = json_encode($value);
        }
        if(!$time) {
            return $this->redis->set($key, $value);
        }
        return $this->redis->setex($key, $time, $value);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key) {
        if(!$key) {
            return '';
        }
        return $this->redis->get($key);
    }

//    /**
//     * @param $key
//     * @param $value
//     * @return mixed
//     */
//    public function sadd($key, $value) {
//        return $this->redis->sAdd($key, $value);
//    }
//
//    /**
//     * @param $key
//     * @param $value
//     * @return int
//     */
//    public function srem($key, $value) {
//        return $this->redis->sRem($key, $value);
//    }

    public function sMembers($key) {
        return $this->redis->sMembers($key);
    }

    /**
     * 使用魔术方法替换前面两个方法，使代码更简洁
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if(count($arguments) != 2) {
            return '';
        }
        return $this->redis->$name($arguments[0], $arguments[1]);
    }
}