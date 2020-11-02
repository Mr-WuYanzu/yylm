<?php

namespace app\otadmins\controller;
use think\Config;
use think\Loader;
use think\Db;

class Index extends Base
{
    public function index()
    {
		return $this->fetch('/index');	
    }


    /**
     * [indexPage 后台首页]
     * @return [type] [description]
     * @author [OUTENG欧腾]
     */
    public function indexPage()
    {
        $info = array(
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'onload'     => ini_get('upload_max_filesize'),
            'think_v'    => THINK_VERSION,
            'phpversion' => phpversion(),
        );
        $this->assign('info',$info);
		
		$article_list=Db::name('article')->where("") -> limit(5) ->order('views desc')->select();
		$this -> assign('article_list', $article_list);


		$log_list=Db::name('log')->where("") -> limit(10)->order('log_id desc')->select();
		foreach($log_list as $k=>$v){
            $log_list[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }  
		$this -> assign('log_list', $log_list);
		
		
		
		
        return $this->fetch('index');
    }


    /**
     * 清除缓存
     */
    public function clear() {
        if (delete_dir_file(CACHE_PATH) || delete_dir_file(TEMP_PATH)) {
            return json(['code' => 1, 'msg' => '清除缓存成功']);
        } else {
            return json(['code' => 0, 'msg' => '清除缓存失败']);
        }
    }

}
