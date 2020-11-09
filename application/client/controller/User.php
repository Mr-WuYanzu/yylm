<?php


namespace app\client\controller;


use app\models\Complaint;
use app\models\Coop;
use app\models\Member;
use think\Controller;

class User extends Base
{
    /*
     * 用户投诉
     */
    public function complain(){
        $data = [
            'uid' => $this->uid,
            'act_id' => request()->post('act_id'),
            'admin_id' => request()->post('admin_id'),
            'cont_id' => request()->post('cont_id'),
            'marks' => request()->post('marks'),
            'phone' => request()->post('phone'),
            'add_time' => time()
        ];
        if(empty($data['cont_id']) || empty($data['act_id'])){
            return json(respond(1000,'参数错误'));
        }
        $complaint_model = new Complaint();
        $res = $complaint_model->add($data);
        if($res){
            return json(respond(200,'成功'));
        }
        return json(respond(1000,'失败'));
    }

    /*
     * 申请合作
     */
    public function applyForCoop(){
        $data = [
            'name' => request()->post('name'),
            'vocation' => request()->post('vocation'),
            'phone'  => request()->post('phone'),
            'company' => request()->post('company'),
            'city' => request()->post('city'),
            'add_time' => time(),
            'province' => request()->post('province'),
            'area' => request()->post('area')
        ];
        if(empty($data['name']) || empty($data['vocation']) || empty($data['phone']) || empty($data['company']) || empty($data['city']) || empty($data['province']) || empty($data['area'])){
            return json(respond(1000,'参数错误'));
        }
        $coop_model = new Coop();
        $res = $coop_model->add($data);
        if($res){
            return json(respond(200,'成功'));
        }
        return json(respond(1000,'失败'));
    }
}