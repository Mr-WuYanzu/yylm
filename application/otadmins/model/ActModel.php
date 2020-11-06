<?php

namespace app\otadmins\model;
use think\Model;
use think\Db;

class ActModel extends Model
{
    protected $name = 'act';

    public function getActByWhere($map, $Nowpage, $limits)
    {
        $order = $this->where($map)->page($Nowpage, $limits)->order('add_time desc')->select();
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
        return $this->where($map)->count();
    }

    public function getOneClassify($map){

        return $this->where($map)->find();
    }

}


