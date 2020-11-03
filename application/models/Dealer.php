<?php


namespace app\models;


use think\Model;

class Dealer extends Model
{
    protected $table = 'think_act_dealer';

    public function getInfoByIds($dealer_ids){
        if(empty($dealer_ids)){
            return [];
        }
        $data = $this->field('id,name')->whereIn('id',$dealer_ids)->select();
        if($data){
            return collection($data)->toArray();
        }
        return [];
    }
}