<?php


namespace app\models;


class Redis
{
    public $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function get($key){
       return $this->redis->get($key);
    }

    public function set($key,$val,$expire=86400){
        $this->redis->set($key,$val,$expire);
    }
}