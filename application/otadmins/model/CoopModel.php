<?php

namespace app\otadmins\model;
use think\Model;
use think\Db;

class CoopModel extends Model
{
    protected $name = 'coop';

    public function getCoopByWhere($map, $Nowpage, $limits)
    {
        $coop = $this->where($map)->page($Nowpage, $limits)->order('add_time desc')->select();
        foreach ($coop as $key=>$vo){
            if($vo['add_time'] != ""){
                $coop[$key]['add_time'] = date('Y-m-d H:i:s',$vo['add_time']);
            }
        }
        return $coop;
    }

    public function getCoopLimit($map,$start){

        $coop = $this->where($map)->limit($start)->order('add_time desc')->select();
        foreach ($coop as $key=>$vo){
            if($vo['add_time'] != ""){
                $coop[$key]['add_time'] = date('Y-m-d H:i:s',$vo['add_time']);
            }
        }
        return $coop;
    }


    public function getAllCount($map)
    {
        return $this->where($map)->count();
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


