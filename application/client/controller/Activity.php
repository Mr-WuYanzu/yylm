<?php
namespace app\client\controller;

use app\models\Order;

class Activity extends \think\Controller
{
    /*
     * 活动列表
     */
    public function actList(){
        $id = request()->get('id');
        $activity_model = new \app\client\model\Activity();
        $data = $activity_model->getInfo($id);
        return json($data);
    }

    /*
     * 排行榜
     */
    public function rank(){
        $id = request()->get('id');
        $page = intval(request()->get('page'))?:1;
        $page_size = intval(request()->get('page_size'))?intval(request()->get('page_size')):10;
        $page_size = $page_size>10?10:$page_size;
        $order_model = new Order();
        $data = $order_model->totalShareRank($id,$page,$page_size);
        return json($data);
    }

    /*
     * 活动详情
     */
    public function details(){
        $id = request()->get('id');
        $dealer_id = request()->get('dealer_id');
        $activity_model = new \app\client\model\Activity();
        $data = $activity_model->getDetails($id,$dealer_id);
        return json($data);
    }
}