<?php


namespace app\models;


use think\Model;

class Complaint extends Model
{
    protected $table = 'think_complaint';

    public function add($data){
        if(empty($data)){
            return false;
        }
        return $this->insert($data);
    }
}