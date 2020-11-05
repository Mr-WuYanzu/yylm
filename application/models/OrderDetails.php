<?php


namespace app\models;


use think\Model;

class OrderDetails extends Model
{
    protected $table = 'think_order_detail';

    /*
     * 批量添加
     */
    public function bathAdd($data){
        if(empty($data)){
            return false;
        }
        return $this->saveAll($data);
    }
}