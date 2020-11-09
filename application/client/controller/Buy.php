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
        $uid = 1;
        $openid = 1;
        $buy_model = new \app\client\model\Buy();
        $res = $buy_model->pay($id,$phone,$uid,$marks,$share_id,$openid);
        return json($res);
    }

    public function payNotify(){
        $data=file_get_contents('php://input');
        $data = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $buy_model = new \app\client\model\Buy();
        $res = $buy_model->payNotify($data);
        if($res === true){
            echo 'success';
        }
    }

    /*
     * 核销接口
     */
    public function cancel(){
        $act_id = request()->post('act_id');
        $pro_id = request()->post('pro_id');
        $pwd = request()->post('pwd');
        $uid = 1;
        $buy_model = new \app\client\model\Buy();
        $res = $buy_model->cancel($act_id,$pro_id,$uid,$pwd);
        return json($res);
    }
}