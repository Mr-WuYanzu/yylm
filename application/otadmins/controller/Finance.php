<?php

namespace app\otadmins\controller;
use app\otadmins\model\FinanceModel;
use think\Db;

class Finance extends Base
{

    /**
     * 交易明细
     * @return mixed|\think\response\Json
     */
    public function transaction(){

        $key = input('key');
        $mark_id = input('mark_id');
        $map = [];
        if($key&&$key!==""){
            $map['uid|nickname'] = ['like',"%" . $key . "%"];
        }
        if($mark_id){
            $map['mark_id'] = $mark_id;
        }else{
            $map['mark_id'] = ['in',[4,5]];
        }
        $reservation = input('reservation','');
        if($reservation){
            $sldate=urldecode($reservation);//获取格式 2015-11-12 - 2015-11-18
            $arr = explode(" - ",$sldate);//转换成数组
            $arrdateone=strtotime($arr[0]);
            $arrdatetwo=strtotime($arr[1].' 23:55:55');
            if($arrdateone && $arrdatetwo){
                $map['add_time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
            }
        }
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 10;// 获取总条数
        $count = Db::name('earnings')->alias('e')->join('think_member m','e.uid = m.id')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
        $article = new FinanceModel();
        $lists = $article->getFinanceByWhere($map, $Nowpage, $limits);
        foreach($lists as $k => $v){
            $lists[$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
        }
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count); 
        $this->assign('val', $key);
        $this->assign('mark_id',$mark_id);
        $this->assign('reservation',$reservation);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }

    public function commission(){

        $key = input('key');
        $mark_id = input('mark_id');
        $map = [];
        if($key&&$key!==""){
            $map['uid|nickname'] = ['like',"%" . $key . "%"];
        }
        if($mark_id){
            $map['mark_id'] = $mark_id;
        }else{
            $map['mark_id'] = ['in',[4,5]];
        }
        $reservation = input('reservation','');
        if($reservation){
            $sldate=urldecode($reservation);//获取格式 2015-11-12 - 2015-11-18
            $arr = explode(" - ",$sldate);//转换成数组
            $arrdateone=strtotime($arr[0]);
            $arrdatetwo=strtotime($arr[1].' 23:55:55');

            if($arrdateone && $arrdatetwo){
                $map['add_time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
            }
        }
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 10;// 获取总条数
        $count = Db::name('earnings')->alias('e')->join('think_member m','e.uid = m.id')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
        $article = new FinanceModel();
        $lists = $article->getFinanceByWhere($map, $Nowpage, $limits);
        foreach($lists as $k => $v){
            $lists[$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
        }
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count);
        $this->assign('val', $key);
        $this->assign('mark_id',$mark_id);
        $this->assign('reservation',$reservation);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }

    /*
     * 佣金明细
     */
    public function commissionDetails(){
        $parents = [
            'user_info' => request()->get('user_info'),
            'user_type' => request()->get('user_type'),
        ];
        $page = request()->get('page')?:1;
        $page_size = request()->get('page_size')?:10;
        $where = [];
        if(!empty($parents['user_info'])){
            #获取用户id
            $user_info = Db::table('think_member')->field('id')->where('id',$parents['user_info'])->whereOr(['nickname'=>['like','%'.$parents['user_info'].'%']])->select();
            if(!empty($user_info)){
                $where['uid'] = ['in',array_column($user_info,'id')];
            }
        }
        if(!empty($parents['user_type'])){
            if($parents['user_type'] == 1){
                $where['user_type'] = ['in',[1,2]];
            }else{
                $where['user_type'] = ['=',$parents['user_type']];
            }
        }
        $reservation = input('reservation','');
        if($reservation){
            $sldate=urldecode($reservation);//获取格式 2015-11-12 - 2015-11-18
            $arr = explode(" - ",$sldate);//转换成数组
            $arrdateone=strtotime($arr[0]);
            $arrdatetwo=strtotime($arr[1].' 23:55:55');
            if($arrdateone && $arrdatetwo){
                $where['add_time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
            }
        }
        $offset = ($page-1)*$page_size;
        #计算总页数
        $count = Db::table('think_earnings')->where($where)->count();
        $page_max = ceil($count/$page_size);
        #获取数据
        $data = Db::table('think_earnings')->where($where)->order('add_time desc')->limit($offset,$page_size)->select();
        if(!empty($data)){
            #处理用户信息
            $uids = array_unique(array_column($data,'uid'));
            $user_infos = Db::table('think_member')->field('id,nickname')->whereIn('id',$uids)->select();
            $nicknames = array_column($user_infos,'nickname','id');
            #处理活动信息
            $act_ids = array_unique(array_column($data,'act_id'));
            $act_infos = Db::table('think_act')->field('id,title,share_num')->whereIn('id',$act_ids)->select();
            $titles = array_column($act_infos,'title','id');
            $share_num = array_column($act_infos,'share_num','id');
            foreach ($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $data[$k]['nickname'] = isset($nicknames[$v['uid']])?$nicknames[$v['uid']]:'';
                $data[$k]['act_name'] = isset($titles[$v['act_id']])?$titles[$v['act_id']]:'';
                $data[$k]['share_num'] = isset($share_num[$v['act_id']])?$share_num[$v['act_id']]:'';
            }
        }
        $this->assign('Nowpage', $page); //当前页
        $this->assign('allpage', $page_max); //总页数
        $this->assign('count', $count);
        $this->assign('where', $parents);
        $this->assign('user_info', $parents['user_info']);
        $this->assign('user_type',$parents['user_type']);
        $this->assign('reservation',$reservation);
        if(input('get.page')){
            return json($data);
        }
        return $this->fetch();
    }
}