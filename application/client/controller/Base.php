<?php


namespace app\client\controller;


use app\models\Redis;
use think\Controller;

class Base extends Controller
{
    protected function _initialize()
    {
//        parent::_initialize(); // TODO: Change the autogenerated stub
        $token = request()->get('token');
        Redis::$redis->get();
    }
}