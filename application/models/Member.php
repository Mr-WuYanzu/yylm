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
       $data = $this->field('id,nickname,head_img')->whereIn('id',$user_ids)->select();
       if($data){
           return collection($data)->toArray();
       }
       return [];
    }

    public function getInfoById($user_id){
        if(empty($user_id)){
            return [];
        }
        $data = $this->field('id,nickname,money,openid,utype')->where('id',$user_id)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    public function getInfoByOpenid($openid){
        if(empty($openid)){
            return [];
        }
        $data = $this->field('id,nickname,money,openid,utype,login_num')->where('openid',$openid)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    public function upd($uid,$data){
        if(empty($uid) || empty($data)){
            return false;
        }
        return $this->where('id',$uid)->update($data);
    }

    public function add($data){
        if(empty($data)){
            return false;
        }
        return $this->insertGetId($data);
    }
}