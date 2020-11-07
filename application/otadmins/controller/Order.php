<?php
namespace app\otadmins\controller;
use app\otadmins\model\OrderDetailModel;
use app\otadmins\model\OrderModel;
use think\Db;
class Order extends Base {


    public function index(){
        $map = array();
        $map['o.status'] = 1;
        /** 商品属性筛选*/
        $type = input('type');
        if($type){
            $map['o.type'] = $type;
        }
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['nickname|phone|unique'] = ['like',"%" . $key . "%"];
        }
        /** 下单时间筛选 */
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
        $order = new OrderModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 15;// 获取总条数
        $count = $order->getAllCount($map);//计算总页面

        $allpage = intval(ceil($count / $limits));
        $lists = $order->getOrderByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count); //总页数
        $this->assign('val', $key);
        $this->assign('type', $type);
        $this->assign('reservation', $reservation);
        if(input('get.page'))
        {
            return json($lists);
        }
        $order_count = $order->calculation();
        $this->assign('order_count',$order_count);
        return $this->fetch();
    }


    public function write_off(){
        $map = array();
        $map['is_cancel'] = 2;
        /** 商品属性筛选*/
        $type = input('type');
        if($type){
            $map['type'] = $type;
        }
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['nickname|phone|unique'] = ['like',"%" . $key . "%"];
        }
        /** 下单时间筛选 */
        $reservation = input('reservation','');
        if($reservation){
            $sldate=urldecode($reservation);//获取格式 2015-11-12 - 2015-11-18
            $arr = explode(" - ",$sldate);//转换成数组
            $arrdateone=strtotime($arr[0]);
            $arrdatetwo=strtotime($arr[1].' 23:55:55');

            if($arrdateone && $arrdatetwo){
                $map['cancel_time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
            }
        }
        /** 订单状态筛选*/
        //$map['state'] = ['eq',$type];
        $order = new OrderDetailModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 15;// 获取总条数
        $count = $order->getAllCount($map);//计算总页面

        $allpage = intval(ceil($count / $limits));
        $lists = $order->getOrderByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count); //总页数
        $this->assign('val', $key);
        $this->assign('type', $type);
        $this->assign('reservation', $reservation);
        if(input('get.page'))
        {
            return json($lists);
        }
        $order_count = $order->calculation();
        $this->assign('order_count',$order_count);
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
            Db::name('order')->where('order_id',$v)->update(['status'=>2]);
        }
        return json(['code'=>1,'msg'=>'已删除']);
    }



    public function excel_order(){
        $map = array();
        $map['o.status'] = 1;
        /** 商品属性筛选*/
        $type = input('type');
        if($type){
            $map['type'] = $type;
        }
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['nickname|phone|unique'] = ['like',"%" . $key . "%"];
        }
        /** 下单时间筛选 */
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
        $order = new OrderModel();
        //大数据需设置脚本时间为不限制
        set_time_limit(0);
        ini_set("memory_limit", "2048M");
        //设置浏览器Header开头，以及CSV文件名
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("UTF-8", "GB18030", "订单列表") . ".csv");//导出文件名
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $column_name = "序号,订单编号,订单类型,用户信息,活动名,实际支付,下单时间";
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
            $export_data = $order->getOrderLimit($map,strval($i * $pre_count) . ",{$pre_count}");
            foreach ($export_data as &$v) {
                $a++;
                //整理数据，顺序需对应$column_name
                $tmpRow = [];
                $tmpRow[] = $a;
                $tmpRow[] = $v['unique']."\"\t";
                if($v['type'] == 1){
                    $tmpRow[] = '单商家拓客';
                }elseif($v['type'] == 2){
                    $tmpRow[] = '多商家拓客';
                }elseif($v['type'] == 3){
                    $tmpRow[] = '共享商圈';
                }elseif($v['type'] == 4){
                    $tmpRow[] = '免单活动';
                }
                $tmpRow[] = $v['nickname'].''.$v['phone'];
                $tmpRow[] = $v['title'];
                $tmpRow[] = $v['pay_price'];
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

    public function excel_order_detail(){
        $map = array();
        /** 商品属性筛选*/
        $type = input('type');
        if($type){
            $map['type'] = $type;
        }
        $key = input('key');
        if($key&&$key!=="")
        {
            $map['nickname|phone|unique'] = ['like',"%" . $key . "%"];
        }
        /** 下单时间筛选 */
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
        $order = new OrderDetailModel();
        //大数据需设置脚本时间为不限制
        set_time_limit(0);
        ini_set("memory_limit", "2048M");
        //设置浏览器Header开头，以及CSV文件名
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("UTF-8", "GB18030", "核销列表") . ".csv");//导出文件名
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $column_name = "序号,订单编号,订单类型,用户信息,核销商家,活动名,服务名称,核销时间";
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
            $export_data = $order->getOrderLimit($map,strval($i * $pre_count) . ",{$pre_count}");
            foreach ($export_data as &$v) {
                $a++;
                //整理数据，顺序需对应$column_name
                $tmpRow = [];
                $tmpRow[] = $a;
                $tmpRow[] = $v['unique']."\"\t";
                if($v['type'] == 1){
                    $tmpRow[] = '单商家拓客';
                }elseif($v['type'] == 2){
                    $tmpRow[] = '多商家拓客';
                }elseif($v['type'] == 3){
                    $tmpRow[] = '共享商圈';
                }elseif($v['type'] == 4){
                    $tmpRow[] = '免单活动';
                }
                $tmpRow[] = $v['nickname'].''.$v['phone'];
                $tmpRow[] = $v['name'];
                $tmpRow[] = $v['title'];
                $tmpRow[] = $v['pro'];
                $tmpRow[] = $v['cancel_time'];

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
//    public function up(){
//        require_once 'extend/PHPExcel/PHPExcel.php';
//        Vendor("PHPExcel.PHPExcel");
//        $path = 'uploads/1111.xlsx';
//        $extension = strtolower( pathinfo($path, PATHINFO_EXTENSION) );
//        if($extension =='xlsx'){
//            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
//        }else if($extension =='xls'){
//            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
//        }
//        $objPHPExcel = $objReader->load($path,$encode='utf-8');
//
//        $sheet = $objPHPExcel->getSheet(0);
//        $highestRow = $sheet->getHighestRow(); // 取得总行数
//        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
//        $data=array();
//        for ($i = 2; $i <= $highestRow; $i++){
//            $item['express_name']=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
//            $item['express_code']=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
//            $data[]=$item;
//        }
//        //拼接sql提高插入速度
//        $query= "insert into think_express (express_name,express_code) values";
//        foreach($data as $row){
//            $query.="('".$row['express_name']."','".$row['express_code']."'),";
//        };
//        $query = substr($query, 0, -1);
//        Db::execute($query);
//    }
    public function xuanshang(){
        $map = array();

        /** 搜索条件筛选*/
        $search_type = input('search_type');

        $key = input('key');
        if($key&&$key!=="")
        {
            //根据会员搜索
            $w['nickname'] = ['like',"%" . $key . "%"];
            $all = Db::name('member')->where($w)->column('member_id');

            $map['member_id'] = ['in',$all];
        }


        /** 下单时间筛选 */
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
        $order = new OrderModel();
        $Nowpage = input('get.page') ? input('get.page'):1;


        $limits = 15;// 获取总条数
        $count = Db::name('xuanshang_order')->where($map)->count();

        $allpage = intval(ceil($count / $limits));
        $lists = Db::name('xuanshang_order')->where($map)->page($Nowpage, $limits)->order('id desc')->select();
        foreach($lists as $k => $v){
            $lists[$k]['nickname'] = Db::name('member')->where('member_id',$v['member_id'])->value('nickname');
            $lists[$k]['x_name'] = Db::name('xuanshang')->where('id',$v['x_id'])->value('a_title');
            $lists[$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
//            $lists[$k]['photo'] = explode(',',$v['photo']);
//            $lists[$k]['photo'] = json_encode($lists[$k]['photo']);
        }

        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count); //总页数
        $this->assign('val', $key);
        $this->assign('reservation', $reservation);
        $this->assign('search_type',$search_type);
        $this->assign('express',Db::name('express')->select());


        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }

    public function cckh(){

        $list = Db::name('xuanshang_order')->where('id = '.input('id'))->find();
        $list['photo'] = substr($list['photo'],0,strlen($list['photo'])-1);
        $list['photo'] = explode(',',$list['photo']);
        return $list['photo'];
    }
    public function xs_tg(){

        $id = input('id');
        $tx_log = Db::name('xuanshang_order')->where('id',$id)->find();
        Db::name('xuanshang_order')->where('id',$id)->update(['state'=>1]);
        $year = date('Y',time());
        $month = date('m',time());
        $day = date('d',time());
        $renwu = Db::name('renwu')->where(['year'=>$year,'month'=>$month,'day'=>$day,'member_id'=>$tx_log['member_id'],'state'=>0])->find();
        if($renwu){
//            var_dump($renwu);die;
            if($renwu['four'] < $renwu['four_num']){
                Db::name('renwu')->where('id',$renwu['id'])->setInc('four',1);
            }
            //调用查询是否完成任务
            $this->cha_jiesuan($renwu['id']);
        }
        echo '1';die;
    }

    public function cha_jiesuan($id){
        $renwu = Db::name('renwu')->where('id',$id)->find();
        if($renwu['state'] == 0){
            if($renwu['one'] == $renwu['one_num'] && $renwu['two'] == $renwu['two_num'] && $renwu['three'] == $renwu['three_num'] && $renwu['four'] == $renwu['four_num'] ){
                Db::name('renwu')->where('id',$id)->update(['state'=>1]);
                $wa = Db::name('wallets')
                    ->where('w_type = 3 and w_state = 0 ')
                    ->select();

                foreach ($wa as $key=>$vo){
                    $a = $vo;
                    //更改状态
                    Db::name('wallets')
                        ->where('w_id = '.$vo['w_id'].'')
                        ->update(['w_state'=>1]);
                    unset($a['w_id']);
                    unset($vo['w_id']);
//                    $vo['w_state'] = 1;
//                    Db::name('wallet')->insert($vo);
                    $a['w_type'] = 3;
                    $a['w_add_time'] = time();
                    $a['w_state'] = 0;
                    Db::name('wallet')->insert($a);

//                    Db::name('member')->where('member_id',$vo['w_member_id'])->setInc('money',$vo['w_money']);
                }
            }
        }
    }

    public function xs_bh(){

        $id = input('id');
        $tx_log = Db::name('tx_log')->where('id',$id)->find();
        Db::name('xuanshang_order')->where('id',$id)->update(['state'=>2]);
        echo '1';die;
    }
}