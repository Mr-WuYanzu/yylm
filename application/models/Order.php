<?php


namespace app\models;


use think\Model;

class Order extends Model
{
    protected $table  = 'think_order';

    /*
     * 统计分享排行
     * @$act_id  活动id
     */
    public function totalShareRank($act_id,$page,$page_size=10){
        if(empty($act_id)){
            return respond(1000,'无数据');
        }
        #获取每单奖金
        $act_model = new Activity();
        $price = $act_model->get_user_brokerage($act_id);
        if($price==0){
            return respond(1000,'无数据');
        }
        $offset = ($page-1)*$page_size;
        $data = $this->field('count(share_id) as share_num,share_id')->order('share_num desc')->order('add_time asc')->group('share_id')->limit($offset,$page_size)->select();
        $count = $this->field('count(distinct share_id)')->count();
        if(!empty($data)){
            $data = collection($data)->toArray();
            #处理用户信息
            $share_ids = array_column($data,'share_id');
            $member_model = new Member();
            $user_info = $member_model->getInfoByIds($share_ids);
            $nicknames = array_column($user_info,'nickname','id');
            $head_imgs = array_column($user_info,'head_img','id');
            foreach ($data as $k=>$v){
                $data[$k]['nickname'] = isset($nicknames[$v['share_id']])?$nicknames[$v['share_id']]:'';
                $data[$k]['head_img'] = isset($head_imgs[$v['share_id']])?$head_imgs[$v['share_id']]:'';
            }
            return respond(200,'成功',['count'=>$count,'page'=>$page,'page_size'=>$page_size,'price'=>$price,'data'=>$data]);
        }
        return respond(200,'成功',[]);
    }

    /*
     * 获取订单信息
     */
    public function getInfoByUnique($unique){
        if(empty($unique)){
            return [];
        }
        $data = $this->where('unique',$unique)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    /*
     * 获取订单信息
     */
    public function getInfoByVolumeId($volume_no){
        if(empty($volume_no)){
            return [];
        }
        $data = $this->where('volume_no',$volume_no)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    /*
    * 获取订单信息
    */
    public function getInfo($order_id='',$is_pay='',$uid='',$act_id=''){
        $where = [];
        if(!empty($order_id)){
            $where['order_id'] = ['=',$order_id];
        }
        if(!empty($is_pay)){
            $where['is_pay'] = ['=',$is_pay];
        }
        if(!empty($uid)){
            $where['uid'] = ['=',$uid];
        }
        if(!empty($act_id)){
            $where['act_id'] = ['=',$act_id];
        }
        $data = $this->where($where)->find();
        if($data){
            return $data->toArray();
        }
        return [];
    }

    public function add($data){
        if(empty($data)){
            return false;
        }
        return $this->insertGetId($data);
    }

    public function upd($id,$data){
        if(empty($id) || empty($data)){
            return false;
        }
        return $this->where('order_id',$id)->update($data);
    }
}