<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-04
 * Time: 17:49
 */
namespace app\admin\controller;
use app\common\lib\Util;

class Image
{
    public function Index()
    {
        $file = request()->file('file');
        $info = $file->move('../public/static/upload');
        if($info) {
            $data = [
                'image' => config('live.host').'/upload/'.$info->getSaveName(),
            ];
            return Util::show(config('code.success'), 'OK', $data);
        } else {
            return Util::show(config('code.error'), 'upload error');
        }
    }
}