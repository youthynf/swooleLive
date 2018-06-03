<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-03
 * Time: 13:16
 */
namespace app\common\lib;

class Util
{
    public static function show($status, $message = '', $data = []) {
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return json_encode($result);
    }
}