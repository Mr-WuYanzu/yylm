<?php


namespace app\client\model;


use app\models\ActivityPro;
use app\models\Dealer;
use app\models\Earnings;
use app\models\Member;
use app\models\Order;
use app\models\OrderDetails;
use app\models\WxPay;
use think\Exception;

class Buy
{
    private $order_model;
    private $activity_model;
    private $act_pro_model;
    private $order_details_model;
    private $wx_pay_model;
    private $member_model;
    private $earnings_model;
    private $dealer_model;
    public $values;

    public function __construct()
    {
        $this->order_model = new Order();
        $this->activity_model = new \app\models\Activity();
        $this->act_pro_model = new ActivityPro();
        $this->order_details_model = new OrderDetails();
        $this->wx_pay_model = new WxPay();
        $this->member_model = new Member();
        $this->earnings_model = new Earnings();
        $this->dealer_model = new Dealer();
    }

    public function pay($id,$phone,$uid,$marks,$share_id,$openid){
        if(empty($id) || empty($phone)){
            return respond(1000,'参数错误');
        }
        #获取活动详情
        $act_info = $this->activity_model->getInfo($id);
        $act_details = $this->act_pro_model->getList($id);
        if(empty($act_info)){
            return respond(1000,'活动不存在');
        }
        if($act_info['start_time']>time()){
            return respond(1000,'活动未开始');
        }
        if($act_info['end_time']<time()){
            return respond(1000,'活动已结束');
        }
        $order_unique = create_order_no('activity_');
        $volume_no = randNum(12);
        while ($this->order_model->getInfoByVolumeId($volume_no)){
            $volume_no = randNum(12);
        }
        if(!empty($share_id) && $act_info['share_type'] == 2){
            #验证分享人是否购买
            $order_data = $this->order_model->getInfo('',2,$share_id,$id);
            if(empty($order_data)){
                $share_id = 0;  #未满足分享条件 取消分享用户
            }
        }
        $time = time();
        $data = [
            'admin_id' => $act_info['admin_id'],
            'uid' => $uid,
            'total_price' => $act_info['price'],
            'pay_price' => $act_info['price'],
            'buynum' => count($act_details),
            'mark' => $marks,
            'unique' => $order_unique,
            'is_pay'    => 1,
            'act_id'    => $id,
            'share_id' => $share_id,
            'type' => 1,
            'volume_no' => $volume_no,
            'phone' => $phone,
            'add_time' => $time,
            'pass_time' => $time+1800#半小时未支付取消订单
        ];
        $this->order_model->startTrans();
        try {
            $order_id = $this->order_model->add($data);
            if(!$order_id){
                throw new Exception('订单创建失败');
            }
            #添加进订单详情表
            $data = [];
            foreach ($act_details as $k=>$v){
                for ($i=0;$i<intval($v['use_num']);$i++){
                    $data[] = [
                        'order_id' => $order_id,
                        'admin_id' => $act_info['admin_id'],
                        'act_id' => $id,
                        'dealer_id' => $act_info['dealer_id'],
                        'pro_id' => $v['id'],
                        'uid' => $uid,
                        'pass_time' => $v['pass_time'],
                        'add_time' => $time
                    ];
                }
            }
            $res = $this->order_details_model->bathAdd($data);
            if(!$res){
                throw new Exception('订单详情创建失败');
            }
            $data = $this->wx_pay_model->wxPay($order_unique,$act_info['price'],'活动抢购','','JSAPI','',$openid);
            if($data['code'] == 1000){
                throw new Exception('支付请求失败');
            }
            $this->order_model->commit();
            return respond(200,'成功',);
        }catch (Exception $exception){
            $this->order_model->rollback();
            return respond(1000,'失败');
        }
    }

    /*
     * 支付异步回调
     */
    public function payNotify($data){
//        if($data['return_code'] == 'SUCCESS'){
            $order_no = $data['out_trade_no'];
            $order_info = $this->order_model->getInfoByUnique($order_no);
            if($order_info['is_pay'] == 2){
                #已经处理过了
                return true;
            }
            if(!empty($order_info)){
                $this->order_model->startTrans();
                try {
                    $admin_brokerage = $price = $order_info['pay_price']-($order_info['pay_price']*config('service_brokerage'));#平台抽取后的金额
                    if($order_info['share_id']){
                        #活动信息
                        $act_info = $this->activity_model->get_brokerage($order_info['act_id']);
                        #有分享人，处理佣金
                        $user_info = $this->member_model->getInfoById($order_info['share_id']);#分享人信息
                        if($act_info['user_brokerage'] + $act_info['dealer_brokerage'] <= $user_info){
                            if($user_info['utype'] == 2){
                                #发起人佣金  减商家佣金
                                $admin_brokerage = $price-$act_info['dealer_brokerage'];
                                $money = $user_info['money']+$act_info['dealer_brokerage'];
                                $user_type = 3;
                                $mark_id = 3;
                            }else{
                                #发起人佣金      减用户佣金
                                $admin_brokerage = $price-$act_info['user_brokerage'];
                                $money = $user_info['money']+$act_info['user_brokerage'];
                                $user_type = 4;
                                $mark_id = 4;
                            }
                            #增加分享人余额
//                            $share_info = $this->member_model->getInfoById($order_info['share_id']);
//                            $money = $user_info['utype'] == 2?$share_info['money']+$act_info['dealer_brokerage']:$share_info['money']+$act_info['user_brokerage'];
//                            $res = $this->member_model->upd($order_info['share_id'],['money'=>$money]);
//                            if(!$res){
//                                throw new Exception('分享人余额增加失败');
//                            }
                            #打款到分享人账户余额
                            if($money){
                                $with_draw_url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
                                $partner_trade_no = md5(microtime());
                                $data = [
                                    'openid' => $user_info['openid'],
                                    'mch_appid' => config('appid'),
                                    'mchid'     => config('mchid'),
                                    'nonce_str' => randNUm(16),
                                    'partner_trade_no' => $partner_trade_no,
                                    'check_name' => 'NO_CHECK',
                                    'amount'    => $money*100,
                                    'desc'      => '零钱提现',
                                ];
                                $this->values=$data;
                                self::SetSign();
                                $xml = self::toxml();
                                $res = self::postXmlCurl($xml, $with_draw_url, $useCert = false, $second = 30);
                                $data = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                                $res = self::record($order_info['admin_id'],$order_info['act_id'],$order_info['share_id'],$user_type,$order_info['order_id'],$money,$mark_id,1,$partner_trade_no);
                                if(!$res){
                                    throw new Exception('打款记录失败');
                                }
//                                if($data['return_code'] == 'SUCCESS'){
//                                    if($data['result_code'] == 'SUCCESS'){
//                                        if(!$res){
//                                            throw new Exception('提现失败');
//                                        }
//                                    }else{
//                                        return json(['code'=>1000,'msg'=>$data['err_code_des']]);
//                                    }
//                                }
//                                return json(['code'=>1000,'msg'=>'提现失败']);
                            }
                        }
                    }
                    #修改发起人用户余额
                    $admin_info = $this->member_model->getInfoById($order_info['admin_id']);
                    $res = $this->member_model->upd($order_info['admin_id'],['money'=>$admin_info['money']+$admin_brokerage]);
                    if(!$res){
                        throw new Exception('发起人余额增加失败');
                    }
                    #发起人收益记录
                    $res = self::record($order_info['admin_id'],$order_info['act_id'],$order_info['admin_id'],2,$order_info['order_id'],$admin_brokerage,2,1,'');
                    if(!$res){
                        throw new Exception('发起人收益记录失败');
                    }
                    #修改订单状态
                    $res = $this->order_model->upd($order_info['order_id'],['is_pay'=>2,'pay_time' => time()]);
                    if(!$res){
                        throw new Exception('处理订单失败');
                    }
                    $this->order_model->commit();
                    return true;
                }catch (Exception $exception){
                    var_dump($exception->getMessage());exit;
                    $this->order_model->rollback();
                    return false;
                }
            }
//        }
        return false;
    }

    /*
     * 打款记录
     */
    public function cancel($act_id,$pro_id,$uid,$pwd){
        if(empty($act_id) || empty($pro_id) || empty($uid) || empty($pwd)){
            return respond(1000,'参数错误');
        }
        #根据产品信息获取产品id
        $act_pro_info = $this->act_pro_model->getinfo($act_id,$pro_id);
        if(empty($act_pro_info)){
            return respond(1000,'产品不存在');
        }
        #验证用户是否购买过此活动
        $order_info = $this->order_model->getInfo('',2,$uid,$act_id);
        if(empty($order_info)){
            return respond(1000,'请购买后再来核销');
        }
        #验证核销密码
        $dealer_pwd = $this->dealer_model->getPwdById($act_pro_info['dealer_id'],$act_id);
        if($dealer_pwd != $pwd){
            return respond(1000,'核销密码错误');
        }
        #获取此产品剩余核销次数
        $order_details = $this->order_details_model->getInfo($act_id,$pro_id,$uid,1,time());
        if(empty($order_details)){
            return respond(1000,'已经没有核销的次数了或活动已过期');
        }
        #核销成功
        $res = $this->order_details_model->upd($order_details['id'],['is_cancel'=>2]);
        if($res){
            return respond(200,'成功');
        }
        return respond(1000,'参数错误');
    }

    /*
     * 打款记录
     */
    private function record($admin_id,$act_id,$uid,$user_type,$order_id,$money,$mark_id,$money_type,$marks){
        #记录打款记录
        $record = [
            'admin_id' => $admin_id,
            'act_id' => $act_id,
            'uid'   => $uid,
            'user_type' => $user_type,
            'order_id' => $order_id,
            'money' => $money,
            'mark_id' => $mark_id,
            'add_time' => time(),
            'money_type' => $money_type,
            'marks' => $marks
        ];
        $res = $this->earnings_model->add($record);
        return $res;
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

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch, CURLOPT_SSLCERT,__DIR__.'/../../../static/wxPay/4718368_yylm.hiyll.com.pem'); //client.

        curl_setopt($ch, CURLOPT_SSLKEY, __DIR__.'/../../../static/wxPay/4718368_yylm.hiyll.com.key');
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
        $sign_str=$str.'&key='.config('key');
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