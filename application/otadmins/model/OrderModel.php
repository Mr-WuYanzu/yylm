<?php

namespace app\otadmins\model;
use think\Model;
use think\Db;

class OrderModel extends Model
{
    protected $name = 'order';

    public function getOrderByWhere($map, $Nowpage, $limits)
    {
        $order = $this->alias('o')
            ->join('think_act m', 'o.act_id = m.id')
            ->join('think_member b','o.uid = b.id')
            ->field('o.*,m.type,m.title,b.nickname')
            ->where($map)->page($Nowpage, $limits)->order('add_time desc')->select();
        foreach ($order as $key=>$vo){
            if($vo['add_time'] != ""){
                $order[$key]['add_time'] = date('Y-m-d H:i:s',$vo['add_time']);
            }
        }
        return $order;
    }

    public function getOrderLimit($map,$start){

        $order = $this->alias('o')
            ->join('think_act m', 'o.act_id = m.id')
            ->join('think_member b','o.uid = b.id')
            ->field('o.*,m.type,m.title,b.nickname')
            ->where($map)->limit($start)->order('add_time desc')->select();
        foreach ($order as $key=>$vo){
            if($vo['add_time'] != ""){
                $order[$key]['add_time'] = date('Y-m-d H:i:s',$vo['add_time']);
            }
        }
        return $order;
    }


    public function getAllCount($map)
    {
        return $this->alias('o')
        ->join('think_act m', 'o.act_id = m.id')
        ->join('think_member b','o.uid = b.id')
        ->where($map)->count();
    }

    public function calculation(){
        $result = [];
        $result['month'] = $this->whereTime('add_time','m')->where(['status'=>1])->count();
        $result['last_month'] = $this->whereTime('add_time','last month')->where(['status'=>1])->count();
        $result['day'] = $this->whereTime('add_time','d')->where(['status'=>1])->count();
        $result['last_day'] = $this->whereTime('add_time','yesterday')->where(['status'=>1])->count();
        $result['zong'] = $this->where(['status'=>1])->count();
        return $result;
    }

}


