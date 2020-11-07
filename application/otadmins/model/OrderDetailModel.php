<?php

namespace app\otadmins\model;
use think\Model;
use think\Db;

class OrderDetailModel extends Model
{
    protected $name = 'order_detail';

    public function getOrderByWhere($map, $Nowpage, $limits)
    {
        $order = $this->alias('o')
            ->join('think_order od','o.order_id = od.order_id')
            ->join('think_act m', 'o.act_id = m.id')
            ->join('think_member b','o.uid = b.id')
            ->join('think_act_dealer d','o.dealer_id = d.id')
            ->field('o.*,m.type,m.title,b.nickname,d.name,od.unique,od.phone')
            ->where($map)->page($Nowpage, $limits)->order('cancel_time desc')->select();
        foreach ($order as $key=>$vo){
            if($vo['cancel_time'] != ""){
                $order[$key]['cancel_time'] = date('Y-m-d H:i:s',$vo['cancel_time']);
            }
            $order[$key]['pro'] = Db::name('act_pro')->where(['id'=>$vo['pro_id']])->value('title');
        }
        return $order;
    }


    public function getOrderLimit($map,$start){


        $order = $this->alias('o')
            ->join('think_order od','o.order_id = od.order_id')
            ->join('think_act m', 'o.act_id = m.id')
            ->join('think_member b','o.uid = b.id')
            ->join('think_act_dealer d','o.dealer_id = d.id')
            ->field('o.*,m.type,m.title,b.nickname,d.name,od.unique,od.phone')
            ->where($map)->limit($start)->order('cancel_time desc')->select();
        foreach ($order as $key=>$vo){
            if($vo['cancel_time'] != ""){
                $order[$key]['cancel_time'] = date('Y-m-d H:i:s',$vo['cancel_time']);
            }
            $order[$key]['pro'] = Db::name('act_pro')->where(['id'=>$vo['pro_id']])->value('title');
        }
        return $order;
    }

    public function getAllCount($map)
    {
        return $this->alias('o')
        ->join('think_order od','o.order_id = od.order_id')
        ->join('think_act m', 'o.act_id = m.id')
        ->join('think_member b','o.uid = b.id')
        ->join('think_act_dealer d','o.dealer_id = d.id')
        ->where($map)->count();
    }


    public function calculation(){
        $result = [];
        $result['month'] = $this->whereTime('add_time','m')->where(['is_cancel'=>2])->count();
        $result['last_month'] = $this->whereTime('add_time','last month')->where(['is_cancel'=>2])->count();
        $result['day'] = $this->whereTime('add_time','d')->where(['is_cancel'=>2])->count();
        $result['last_day'] = $this->whereTime('add_time','yesterday')->where(['is_cancel'=>2])->count();
        $result['zong'] = $this->where(['is_cancel'=>2])->count();
        return $result;
    }

}


