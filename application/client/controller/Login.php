<?php


namespace app\client\controller;


use app\models\Member;
use app\models\Redis;
use think\Controller;

class Login extends Controller
{
    protected $redirect_url = 'https://yylm.hiyll.com/client/user/authRedirect';

    /*
     * 网页授权登录
     */
    public function login(){
        $url = 'https://yylm.hiyll.com/h5/index.html#/';
        $refer_url = request()->get('refer_url')?:'';
        $refer_url = $url.$refer_url;
        $http_referer = base64_encode($refer_url);
        $uri = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $uri .= 'appid='.config('appid').'&redirect_uri='.urlencode($this->redirect_url).'&response_type=code&scope=snsapi_userinfo&state='.$http_referer.'#wechat_redirect';
        header("Location:".$uri);
    }

    /*
     * 网页授权回调
     */
    public function authRedirect(){
        $code=$_GET['code'];
        $state = base64_decode($_GET['state']);
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.config('appid').'&secret='.config('appsecret').'&code='.$code.'&grant_type=authorization_code';
        $arr=json_decode(file_get_contents($url),true);
        if(empty($arr)){
            exit('授权失败');
        }
        //获取用户信息
        $userInfo=file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN');
        $user_info = json_decode($userInfo,true);
        if(empty($user_info)){
            #授权失败
            exit('授权失败');
        }
        $data = [
            'openid' => isset($user_info['openid'])?$user_info['openid']:'',
            'account' => $user_info['nickname']?:'',
            'sex'       => $user_info['sex']?:'',
            'head_img'    => $user_info['headimgurl']?:''
        ];
        if(strpos($state,'?')){
            $state .= '&nickname='.$data['nickname'];
        }else{
            $state .= '?nickname='.$data['nickname'];
        }

        $user_model = new Member();
        #根据用户openid获取用户信息
        $user_info = $user_model->getInfoByOpenid($data['openid']);
        $redis_model = new Redis();
        if($user_info){
            $data['login_num'] = $user_info['login_num']+1;
            $data['update_time'] = time();
            $user_model->upd($user_info['id'],$data);
            $token = md5($data['token'].'_'.$user_info['id']);
            $state .= '&token='.$token;
            $data['uid'] = $user_info['id'];
            $redis_model->set($token,json_encode($data));
            header("Refresh:0;url=".$state);
        }else{
            $data['create_time'] = time();
            $data['update_time'] = time();
            #没有此用户，添加进数据库
            $uid = $user_model->add($data);
            if($uid){
                $token = md5($data['token'].'_'.$uid);
                $data['uid'] = $uid;
                $redis_model->set($token,json_encode($data));
                $state .= '&token='.$token;
                header("Refresh:0;url=".$state);
            }else{
                exit('授权失败');
            }
        }
    }
}