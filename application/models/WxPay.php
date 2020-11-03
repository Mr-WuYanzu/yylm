<?php


namespace app\models;



class WxPay
{
    protected $values=[];
    /*
     * 微信支付
     */
    public function wxPay($order_no,$price,$marks,$notify_url,$type='JSAPI',$scene_info='',$openid=''){
        $info=[
            'appid'		=>	getenv('weixin.appid'),
            'mch_id'	=>	getenv('weixin.mchid'),
            'nonce_str'	=>	randStr(16),
            'sign_type'	=>	'MD5',
            'body'		=>  $marks,
            'out_trade_no'	=>	$order_no,
            'total_fee'	=>	$price*100,
            'spbill_create_ip'	=>	$_SERVER['REMOTE_ADDR'],
            'notify_url'	=> 	$notify_url,
            'trade_type'	=> $type
        ];
        if($type=='JSAPI'){
            $info['openid'] = $openid;
        }else if($type == 'MWEB'){
            $info['scene_info'] = $scene_info;
        }else if($type == 'NATIVE'){
            if(!empty($scene_info)){
                $info['scene_info'] = $scene_info;
            }
        }else if($type == 'APP'){
            $info['appid'] = getenv('app.appid');
        }else if($type == 'APPLET'){
            #小程序支付
            $info['trade_type'] = 'JSAPI';
            $info['appid'] = getenv('applet.appid');
        }
        $this->values=$info;
        self::SetSign();
        $xml = self::toxml();
        $res = self::postXmlCurl($xml, WEIXIN_UNIFIEDORDER_URL, $useCert = false, $second = 30);
        $data = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
            $response = [
                'code' => 200,
                'msg'  => '成功',
                'prepay_id' => $data['prepay_id']
            ];
            if($type=='JSAPI'){
                $prepay_id = $data['prepay_id'];
                #jsapi支付
                self::jsapi($prepay_id,getenv('weixin.appid'));
                $this->values['order_no'] = $order_no;
                $response['data'] = $this->values;
            }elseif($type=="MWEB"){
                $response['data'] = $data['mweb_url'];
            }else if($type == 'NATIVE'){
                $response['data'] = $data['code_url'];
            }else if($type == 'APPLET'){
                $prepay_id = $data['prepay_id'];
                self::jsapi($prepay_id,getenv('applet.appid'));
                $this->values['order_no'] = $order_no;
                $response['data'] = $this->values;
            }
            return $response;
        }else{
            $response = [
                'code' => 1000,
                'msg'  => '失败',
                'data' => $data
            ];
            return $response;
        }
    }

    /*
     * 微信退款
     * @order_no 支付订单号
     * @out_refund_no  退款订单号
     * @$total_fee  订单金额
     * @refund_fee  退款金额
     * @notify_url  异步通知地址
     */
    public function wxPayment($order_no,$out_refund_no,$total_fee,$refund_fee,$notify_url=''){
        $info=[
            'appid'		=>	getenv('weixin.appid'),
            'mch_id'	=>	getenv('weixin.mchid'),
            'nonce_str'	=>	randStr(16),
            'sign_type'	=>	'MD5',
            'out_trade_no' => $order_no,
            'out_refund_no' => $out_refund_no,
            'total_fee' => $total_fee*100,
            'refund_fee' => $refund_fee,
        ];
        if(!empty($notify_url)){
            $info['notify_url'] = $notify_url;
        }
        $this->values=$info;
        self::SetSign();
        $xml = self::toxml();
        $res = self::postXmlCurl($xml, WEIXIN_PAYMENT_URL, $useCert = false, $second = 30);
        $data = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
            $response = [
                'code' => 200,
                'msg'  => '成功',
                'data' => []
            ];
            return $response;
        }else{
            $response = [
                'code' => 1000,
                'msg'  => '失败',
                'data' => []
            ];
            return $response;
        }
    }

    /*
     * jsapi
     */
    private function jsapi($prepay_id,$appid){
        $info = [
            'appId' => $appid,
            'timeStamp' => time(),
            'nonceStr' => randStr(16),
            'package'   => 'prepay_id='.$prepay_id,
            'signType'  => 'MD5'
        ];
        $this->values=$info;
        self::SetSign();
    }

    //将数据转换为xml形式
    private function toxml(){
        if(!is_array($this->values)||count($this->values)<=0){
            die('数据格式异常');
        }
        $xml='<xml>';
        foreach($this->values as $k=>$v){
            if(is_numeric($v)){
                $xml .= '<'.$k.'>'.$v.'</'.$k.'>';
            }else{
                $xml .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
            }
        }
        $xml.='</xml>';
        return $xml;
    }

    private  function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            die("curl出错，错误码:$error");
        }
    }
    //生成签名
    private function SetSign(){
        $sign = self::makeSign();
        $this->values['sign']=$sign;
        return $sign;
    }
    //制作签名
    private function makeSign(){
        //第一步,排序签名,对参数按照key=value的格式，并按照参数名ASCII字典序排序
        Ksort($this->values);
        $str = self::ToUrlParams();
        //第二步,拼接API密钥并加密
        $sign_str=$str.'&key='.getenv('weixin.key');
        $sign=MD5($sign_str);
        //第三步,将所有的字符转换为大写
        $string=strtoupper($sign);
        return $string;
    }

    private function ToUrlParams(){
        $str='';
        foreach($this->values as $k=>$v){
            if($k!='sign'&&$v!=''&&!is_array($v)){
                $str .= $k.'='.$v.'&';
            }
        }
        $str=trim($str,'&');
        return $str;
    }
}