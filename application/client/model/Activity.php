<?php
namespace app\client\model;

use app\models\ActivityPro;
use app\models\Dealer;
use app\models\Redis;

class Activity
{
    private $activity_model;
    private $act_pro_model;
    private $dealer_model;

    public function __construct()
    {
        $this->activity_model = new \app\models\Activity();
        $this->act_pro_model = new ActivityPro();
        $this->dealer_model = new Dealer();
    }

    /*
     * 活动详情
     */
    public function getInfo($id){
        if(empty($id)){
            return respond(1000,'参数错误');
        }
        $data = $this->activity_model->getInfo($id);
        if(!empty($data)){
            $data['visit_num'] = intval($data['vi_uv'])+intval($data['click_num']);     #访问数
            $data['share_num'] = intval($data['vi_share_num'])+intval($data['share_num']);      #分享数
            $data['buy_num']  = intval($data['vi_buy_num'])+intval($data['buy_num']);       #报名数
            unset($data['vi_buy_num']);
            unset($data['vi_uv']);
            unset($data['vi_jion_num']);
            unset($data['vi_share_num']);
            $data['start_time'] = date('Y-m-d H:i:s',$data['start_time']);
            $data['end_time'] = date('Y-m-d H:i:s',$data['end_time']);
            #获取详情
            $act_details = $this->act_pro_model->getList($id);
            #处理商家信息
            $dealer_ids = array_unique(array_column($act_details,'dealer_id'));
            $dealer_info = $this->dealer_model->getInfoByIds($dealer_ids);
            $dealer_names = array_column($dealer_info,'name','id');
            $act_data = [];
            foreach ($act_details as $k=>$v){
                $act_details[$k]['dealer_name'] = isset($dealer_names[$v['dealer_id']])?$dealer_names[$v['dealer_id']]:'';
                $act_details[$k]['pass_time'] = date('Y-m-d H:i:s',$v['pass_time']);
                $act_data[$v['dealer_id']][] = $act_details[$k];
            }
            array_multisort($act_data);
            $data['details'] = $act_data;
        }
        return respond(200,'成功',$data);
    }
}