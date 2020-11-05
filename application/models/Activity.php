<?php


namespace app\models;


use think\Model;

class Activity extends Model
{
    protected $table = 'think_act';

    public function getInfo($id){
        if(empty($id)){
            return [];
        }
        $where = [
            'id'=>['=',$id]
        ];
        $data = $this
            ->field('id,title,sub_title,type,admin_id,main_img,post_img,server_code,price,num,admin_phone,vi_buy_num,vi_uv,vi_jion_num,vi_share_num,is_show_award,start_time,end_time,music_id,content,share_type,video,add_time,click_num,share_num,buy_num')
            ->where($where)
            ->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    /*
     * 获取用户佣金
     */
    public function get_user_brokerage($act_id){
        if(empty($act_id)){
            return 0;
        }
        return $this->where('id',$act_id)->value('user_brokerage')?:0;
    }

    /*
     * 获取佣金信息
     */
    public function get_brokerage($act_id){
        if(empty($act_id)){
            return [];
        }
        $data = $this->field('user_brokerage,dealer_brokerage,admin_brokerage')->where('id',$act_id)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }
}