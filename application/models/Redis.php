<?php


namespace app\models;


class Redis
{
    public static $redis;

    public function __construct()
    {
        self::$redis = new \Redis();
        self::$redis->connect('127.0.0.1', 6379);
    }

    public function get($key){
       return self::$redis->get($key);
    }

    public function set($key,$value,$expire){
        self::$redis->set($key,$value,$expire);
    }
}