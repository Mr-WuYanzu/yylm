<?php


namespace app\models;


use think\Model;

class Dealer extends Model
{
    protected $table = 'think_act_dealer';

    public function getInfoByIds($dealer_ids,$act_id=''){
        if(empty($dealer_ids)){
            return [];
        }
        $where = [
            'id' => ['in',$dealer_ids]
        ];
        if(!empty($act_id)){
            $where['act_id'] = $act_id;
        }
        $data = $this->field('id,name,cost_img')->where($where)->select();
        if($data){
            return collection($data)->toArray();
        }
        return [];
    }

    public function getInfoById($dealer_id,$act_id=''){
        if(empty($dealer_id)){
            return [];
        }
        $where = [
            'id' => ['=',$dealer_id]
        ];
        if(!empty($act_id)){
            $where['act_id'] = $act_id;
        }
        $data = $this->field('id,name,cost_img,sit,long,dime,act_id,marks,phone')->where($where)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }
}