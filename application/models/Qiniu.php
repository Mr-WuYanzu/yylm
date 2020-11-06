<?php


namespace app\models;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Model;

class Qiniu extends Model
{
    protected $table = 'wm_user_coupon';
    public function shareUpload($filePath,$ext=[]){
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey ="Q7LsnhepMNryFJ9X2lnrdIRVWc0dmvogJG8oLBid";
        $secretKey = "desdQzc8F0F5G_I4JZ8IVIMnBZ7CeT1F3x96eBoo";
        $bucket = "wmanager";
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 上传到七牛后保存的文件名
        $key = self::randName(time()).'.png';
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return ['code'=>1000,'msg'=>'错误'];
        } else {
            return ['code'=>200,'msg'=>'成功','data'=>$ret];
        }
    }

    /*
     * 随机生成文件名
     */
    private static function randName($file_name){
        $time = microtime();
        return md5($file_name.$time);
    }
}