<?php


namespace app\models;


use think\Model;

class Coop extends Model
{
    protected $table = 'think_coop';

    public function add($data){
        if(empty($data)){
            return false;
        }
        return $this->insert($data);
    }
}