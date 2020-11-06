<?php
namespace app\client\model;

use app\models\ActivityPro;
use app\models\Dealer;
use app\models\Member;
use app\models\Order;
use app\models\Qiniu;
use app\models\Redis;
use Endroid\QrCode\QrCode;

class Activity
{
    private $activity_model;
    private $act_pro_model;
    private $dealer_model;
    private $member_model;
    private $order_model;

    public function __construct()
    {
        $this->activity_model = new \app\models\Activity();
        $this->act_pro_model = new ActivityPro();
        $this->dealer_model = new Dealer();
        $this->member_model = new Member();
        $this->order_model = new Order();
    }

    /*
     * 活动详情
     */
    public function getInfo($id){
        if(empty($id)){
            return respond(1000,'参数错误');
        }
        $data = $this->activity_model->getInfo($id);
        if(!empty($data)){
            $data['visit_num'] = intval($data['vi_uv'])+intval($data['click_num']);     #访问数
            $data['share_num'] = intval($data['vi_share_num'])+intval($data['share_num']);      #分享数
            $data['buy_num']  = intval($data['vi_buy_num'])+intval($data['buy_num']);       #报名数
            unset($data['vi_buy_num']);
            unset($data['vi_uv']);
            unset($data['vi_jion_num']);
            unset($data['vi_share_num']);
            $data['start_time'] = date('Y-m-d H:i:s',$data['start_time']);
            $data['end_time'] = date('Y-m-d H:i:s',$data['end_time']);
            #获取详情
            $act_details = $this->act_pro_model->getList($id);
            #处理商家信息
            $dealer_ids = array_unique(array_column($act_details,'dealer_id'));
            $dealer_info = $this->dealer_model->getInfoByIds($dealer_ids,$id);
            $dealer_names = array_column($dealer_info,'name','id');
            $dealer_img = array_column($dealer_info,'cost_img','id');
            $act_data = [];
            foreach ($act_details as $k=>$v){
                $act_details[$k]['dealer_name'] = isset($dealer_names[$v['dealer_id']])?$dealer_names[$v['dealer_id']]:'';
                $act_details[$k]['dealer_img'] = isset($dealer_img[$v['dealer_id']])?$dealer_img[$v['dealer_id']]:'';
                $act_details[$k]['pass_time'] = date('Y-m-d H:i:s',$v['pass_time']);
                $act_data[$v['dealer_id']][] = $act_details[$k];
            }
            array_multisort($act_data);
            $data['details'] = $act_data;
        }
        return respond(200,'成功',$data);
    }

    public function getDetails($id,$dealer_id){
        if(empty($id) || empty($dealer_id)){
            return respond(1000,'参数错误');
        }
        $dealer_info = $this->dealer_model->getInfoById($dealer_id,$id);
        $activity_details = $this->act_pro_model->getList($id,$dealer_id);
        foreach ($activity_details as $k=>$v){
            $activity_details[$k]['pass_time'] = date('Y-m-d H:i:s',$v['pass_time']);
        }
        return respond(200,'成功',['data'=>$activity_details,'dealer_info'=>$dealer_info]);
    }

    /*
     * 活动分享
     */
    public function share($act_id,$uid){
        $img_url = 'https://business.niushishop.com';
        if(empty($act_id) || empty($uid)){
            return json(['code'=>1000,'msg'=>'参数错误']);
        }
        $act_info = $this->activity_model->getInfo($act_id);
        if($act_info['share_type'] == 2){
            #验证此用户是否购买过
            $res = $this->order_model->getInfo('',2,$uid,$act_id);
            if(!$res){
                return json(['code'=>1000,'msg'=>'请购买后再分享']);
            }
        }
        $url = 'https://yylm.hiyll.com/';
        #获取用户信息
        $user_info = $this->member_model->getInfoById($uid);
        if(empty($user_info)){
            return json(['code'=>1000,'msg'=>'失败']);
        }
        $qrcode = new QrCode($url);
        $name = rand(1,99999999).time();
        if(!is_dir('qrcode')){
            mkdir('qrcode');
        }
        $path =__DIR__.'/../../../public/qrcode/'.$name.'.jpg';
        $qrcode->writeFile($path);
        $res = $this->createImg($path,$user_info['head_img'],$user_info['nickname'],'https://business.niushishop.com/'.$act_info['post_img']);
        if(!$res){
            return json(['code'=>1001,'msg'=>'失败']);
        }
        #生成成功返回前端图片路径
        if($res['code'] == 200){
            return respond('200','成功',$img_url.'/'.$res['data']['key']);
        }else{
            return respond('1000','失败');
        }
    }

    /*
     * 生成图片
     */
    public function createImg($qrcode_path,$head_img,$user_name,$back_img){
//        $ch = curl_init($head_img); //$url是微信的图像地址
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $content = curl_exec($ch);
////        curl_close($ch);
//        $head_img=time().rand(1,9999).'.jpg';
//        file_put_contents(__DIR__.'/../../../public/images/'.$head_img,$content);
        $config = [
            'image'=>[
                [
//                    'url'=>__DIR__.'/../../../public/images/'.$head_img,     //头像地址
                    'url'=>$head_img,     //头像地址
                    'is_yuan'=>false,          //true图片圆形处理
                    'stream'=>0,
                    'left'=>20,               //小于0为小平居中
                    'top'=>50,
                    'right'=>0,
                    'width'=>120,             //图像宽
                    'height'=>120,            //图像高
                    'opacity'=>100            //透明度
                ],
                [
                    'url'=>$qrcode_path,     //二维码地址
                    'is_yuan'=>false,          //true图片圆形处理
                    'stream'=>0,
                    'left'=>30,               //小于0为小平居中
                    'top'=>50,
                    'right'=>0,
                    'width'=>185,             //图像宽
                    'height'=>185,            //图像高
                    'opacity'=>100,            //透明度
                    'type' => 1     // 特殊处理 计算宽高
                ],
            ],
            'text'=>[
                [
                    'text'=>$user_name,            //用户昵称
                    'left'=>160,                              //小于0为小平居中
                    'top'=>120,
                    'fontSize'=>23,                         //字号
                    'fontColor'=>'255,255,255',                //字体颜色
                    'angle'=>0,
                ]
            ],
            'background'=>$back_img,          //背景图
        ];
//        echo file_get_contents('simhei.ttf');exit;
        is_dir('share')?:mkdir('share');
        $filename = __DIR__.'/../../../public/share/'.time().'.jpg';
        //$filename为空是真接浏览器显示图片
        $res = $this->createPoster($config,$filename);
        unlink($filename);
        unlink($qrcode_path);
        return $res;
    }

    function createPoster($config = array() , $filename = "") {
        //如果要看报什么错，可以先注释调这个header
        //if(empty($filename)) header("content-type: image/png");
        if (empty($filename)) header("content-type: image/png;charset=utf-8");
        $font_path = __DIR__.'/../../../public/font/simhei.ttf';
        $imageDefault = array(
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 100,
            'height' => 100,
            'opacity' => 100
        );
//        $textDefault = array(
//            'text' => '',
//            'left' => 0,
//            'top' => 0,
//            'fontSize' => 32, //字号
//            'fontColor' => '255,255,255', //字体颜色
//            'angle' => 0,
//        );
        $background = $config['background']; //海报最底层得背景
        //背景方法
        $backgroundInfo = getimagesize($background);
        $backgroundFun = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
        $background = $backgroundFun($background);
        $backgroundWidth = imagesx($background); //背景宽度
        $backgroundHeight = imagesy($background); //背景高度
        $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background) , imagesy($background) , imagesx($background) , imagesy($background));
        //处理了图片
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val = array_merge($imageDefault, $val);
                $info = getimagesize($val['url']);

                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if ($val['stream']) { //如果传的是字符串图像流
                    $info = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res = $function($val['url']);
                $resWidth = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //如果是透明的gif或png做透明处理
                $ext = pathinfo($val['url']);
                if (array_key_exists('extension',$ext)) {
                    if ($ext['extension'] == 'gif' || $ext['extension'] == 'png') {
                        imageColorTransparent($canvas, $color); //颜色透明

                    }
                }
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                //$val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']) - $val['width']:$val['left'];
                //如果left小于-1我这做成了计算让其水平居中
                if ($val['left'] < 0) {
                    $val['left'] = ceil($backgroundWidth - $val['width']) / 2;
                }
                $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                if(isset($val['type']) && $val['type'] == 1){
                    $val['left'] = $backgroundWidth-$val['left']-$val['width'];
                    $val['top'] = $backgroundHeight-$val['top']-$val['height'];
                }
                //放置图像
                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']); //左，上，右，下，宽度，高度，透明度
            }
        }
        //处理文字
        if (!empty($config['text'])) {
            foreach ($config['text'] as $key => $val) {
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
                //$val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']):$val['left'];
                //如果left小于-1我这做成了计算让其水平居中
                if ($val['left'] < 0) {
                    $fontBox = imagettfbbox($val['fontSize'], 0, $font_path, $val['text']); //文字水平居中实质
                    $val['left'] = ceil(($backgroundWidth - $fontBox[2]) / 2); //计算文字的水平位置
                }
                $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
                if(isset($val['weight'])){
                    imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $font_path, $val['text']);
                    imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $font_path, $val['text']);
                }else{
                    imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $font_path, $val['text']);
                }
//                if(isset($val['is_inline'])){
//                    $font_size = ceil($val['fontSize']/2);
//                    $top = $val['top']-$font_size;
//                    $num = $val['fontSize']*mb_strlen($val['text']);
//                    $bg = imagecolorallocate($imageRes,128,128,128);
//                    imageline($imageRes, $val['left'], $top, $val['left']+$num, $top, $bg);
//                }
            }
        }
        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($imageRes, $filename, 90); //保存到本地
            imagedestroy($imageRes);
            if (!$res){
                return false;
            }
            #保存到七牛云
            $qiniu_model = new Qiniu();
            $res = $qiniu_model->shareUpload($filename);
            return $res;
        } else {
            header("Content-type:image/png");
            imagejpeg($imageRes); //在浏览器上显示
            imagedestroy($imageRes);
        }
    }
}