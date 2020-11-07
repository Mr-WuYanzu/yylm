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
}