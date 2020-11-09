<?php


namespace app\client\controller;


use think\Controller;

class Pay extends Controller
{
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