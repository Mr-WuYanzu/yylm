<?php


namespace app\client\controller;


use think\Controller;

class Buy extends Base
{
    public function pay(){
        $id = request()->post('id');#活动id
        $phone = request()->post('phone');
        $marks = request()->post('marks');
        $share_id = request()->post('share_id');
        $uid = $this->uid;
        $openid = $this->openid;
        $buy_model = new \app\client\model\Buy();
        $res = $buy_model->pay($id,$phone,$uid,$marks,$share_id,$openid);
        return json($res);
    }

    /*
     * 核销接口
     */
    public function cancel(){
        $act_id = request()->post('act_id');
        $pro_id = request()->post('pro_id');
        $pwd = request()->post('pwd');
        $uid = $this->uid;
        $buy_model = new \app\client\model\Buy();
        $res = $buy_model->cancel($act_id,$pro_id,$uid,$pwd);
        return json($res);
    }
}