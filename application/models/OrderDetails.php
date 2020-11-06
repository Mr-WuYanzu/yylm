<?php


namespace app\models;


use think\Model;

class OrderDetails extends Model
{
    protected $table = 'think_order_detail';

    /*
     * 批量添加
     */
    public function bathAdd($data){
        if(empty($data)){
            return false;
        }
        return $this->saveAll($data);
    }

    /*
     * 批量添加
     */
    public function upd($id,$data){
        if(empty($id) || empty($data)){
            return false;
        }
        return $this->where('id',$id)->update($data);
    }

    public function getInfo($act_id='',$pro_id='',$uid='',$is_cancel='',$pass_time=''){
        $where = [];
        if(!empty($act_id)){
            $where['act_id'] = ['=',$act_id];
        }
        if(!empty($pro_id)){
            $where['pro_id'] = ['=',$pro_id];
        }
        if(!empty($uid)){
            $where['uid'] = ['=',$uid];
        }
        if(!empty($is_cancel)){
            $where['is_cancel'] = ['=',$is_cancel];
        }
        if(!empty($pass_time)){
            $where['pass_time'] = ['>',$pass_time];
        }
        $data = $this->field('id,is_cancel')->where($where)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }
}