<?php


namespace app\models;


use think\Model;

class Earnings extends Model
{
    protected $table = 'think_earnings';

    public function add($data){
        if(empty($data)){
            return false;
        }
        return $this->insert($data);
    }
}