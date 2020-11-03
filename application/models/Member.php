<?php


namespace app\models;


use think\Model;

class Member extends Model
{
    protected $table = 'think_member';

    public function getInfoByIds($user_ids){
       if(empty($user_ids)){
           return [];
       }
       $data = $this->field('id,nickname')->whereIn('id',$user_ids)->select();
       if($data){
           return collection($data)->toArray();
       }
       return [];
    }
}