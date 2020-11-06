<?php
namespace app\otadmins\controller;
use app\otadmins\model\CoopModel;
use think\Db;
class Coop extends Base {


    public function index(){
        $map = array();
//        /** 商品属性筛选*/
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['name|phone|city'] = ['like',"%" . $key . "%"];
        }
//        /** 下单时间筛选 */
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
        /** 订单状态筛选*/
        //$map['state'] = ['eq',$type];
        $order = new CoopModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 15;// 获取总条数
        $count = $order->getAllCount($map);//计算总页面

        $allpage = intval(ceil($count / $limits));
        $lists = $order->getCoopByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count); //总页数
        $this->assign('val', $key);
        $this->assign('reservation', $reservation);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }
    /**
     * 一键删除
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del_all(){
        $val = json_decode(input('val'),true);
        if(empty($val)){
            return json(['code'=>-1,'msg'=>'请选择要删除的订单']);
        }
        foreach ($val as $k=>$v){
            Db::name('coop')->where('id',$v)->delete();
        }
        return json(['code'=>1,'msg'=>'已删除']);
    }



    public function excel_coop(){
        $map = array();
//        /** 商品属性筛选*/
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['name|phone|city'] = ['like',"%" . $key . "%"];
        }
//        /** 下单时间筛选 */
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
        $order = new CoopModel();
        //大数据需设置脚本时间为不限制
        set_time_limit(0);
        ini_set("memory_limit", "2048M");
        //设置浏览器Header开头，以及CSV文件名
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("UTF-8", "GB18030", "申请列表") . ".csv");//导出文件名
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $column_name = "序号,联系人,联系电话,公司名称,行业,省份,城市,县区,申请时间";
        $column_name = explode(',', $column_name);
        // 将中文标题转换编码，否则乱码
        foreach ($column_name as $i => $v) {
            $column_name[$i] = iconv('utf-8', 'GB18030', $v);
        }
        // 将标题名称通过fputcsv写到文件句柄
        fputcsv($fp, $column_name);
        $total_export_count =$order->getAllCount($map);//计算总页面
        $pre_count = 1000;
        $a = 0;
        for ($i = 0; $i < intval($total_export_count / $pre_count) + 1; $i++) {
            //切割每份数据
            $export_data = $order->getCoopLimit($map,strval($i * $pre_count) . ",{$pre_count}");
            foreach ($export_data as &$v) {
                $a++;
                //整理数据，顺序需对应$column_name
                $tmpRow = [];
                $tmpRow[] = $a;
                $tmpRow[] = $v['name'];
                $tmpRow[] = $v['phone'];
                $tmpRow[] = $v['company'];
                $tmpRow[] = $v['vocation'];
                $tmpRow[] = $v['province'];
                $tmpRow[] = $v['city'];
                $tmpRow[] = $v['area'];
                $tmpRow[] = $v['add_time'];
                $rows = array();
                foreach ($tmpRow as $export_obj) {
                    $rows[] = iconv('utf-8', 'GB18030', $export_obj);
                }
                fputcsv($fp, $rows);
            }

            // 将已经写到csv中的数据存储变量销毁，释放内存占用
            unset($export_data);
            ob_flush();
            flush();
        }
        exit ();
    }
}