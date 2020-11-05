<?php


namespace app\client\controller;


use think\Controller;

class Buy extends Controller
{
    public function pay(){
        $id = request()->get('id');#活动id
        $phone = request()->get('phone');
        $marks = request()->get('marks');
        $share_id = request()->get('share_id');
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
}