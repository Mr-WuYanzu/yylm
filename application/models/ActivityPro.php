<?php


namespace app\models;


use think\Model;

class ActivityPro extends Model
{
    protected $table = 'think_act_pro';

    public function getList($act_id='',$dealer_id=''){
        $where = [];
        if(!empty($act_id)){
            $where['act_id'] = ['=',$act_id];
        }
        if(!empty($dealer_id)){
            $where['dealer_id'] = ['=',$dealer_id];
        }
        $data = $this->field('id,admin_id,act_id,dealer_id,title,use_num,pass_time')->where($where)->select();
        if($data){
            return collection($data)->toArray();
        }
        return [];
    }
}