<?php

namespace app\otadmins\controller;
use app\otadmins\model\AdminModel;
use app\otadmins\model\MemberModel;
use app\otadmins\model\MemberGroupModel;
use think\Db;

class Member extends Base
{

    //*********************************************会员列表*********************************************//
    /**
     * 会员列表
     * @author [OUTENG欧腾]
     */
    public function index(){

        $key = input('key');
        $map['closed'] = 0;//0未删除，1已删除
        if($key&&$key!=="")
        {
            $map['nickname'] = ['like',"%" . $key . "%"];
        }
        $member = new MemberModel();       
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 10;// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));       
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);   
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('val', $key);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }


    /**
     * 添加会员
     * @author [OUTENG欧腾]
     */
    public function add_member()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $group = new MemberGroupModel();
        $this->assign('group',$group->getGroup());
        return $this->fetch();
    }


    /**
     * 编辑会员
     * @author [OUTENG欧腾]
     */
    public function edit_member()
    {
        $member = new MemberModel();
        if(request()->isAjax()){
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $flag = $member->editMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $group = new MemberGroupModel();
        $this->assign([
            'member' => $member->getOneMember($id),
            'group' => $group->getGroup()
        ]);
        return $this->fetch();
    }


    /**
     * 删除会员
     * @author [OUTENG欧腾]
     */
    public function del_member()
    {
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * 会员状态
     * @author [OUTENG欧腾]
     */
    public function member_status()
    {
        $id = input('param.id');
        $status = Db::name('member')->where('id',$id)->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }

    public function add_remark(){

        $request = input('post.');
        Db::name('member')->where('id',$request['member_id'])->update(['marks'=>$request['remark']]);
        return json(['code' => 1, 'data' => [], 'msg' => '添加成功']);
    }

    public function get_marks(){
        $id = input('id');
        $member = new MemberModel();
        $result = $member->getOneMember($id);
        return json(['code' => 1, 'data' => $result, 'msg' => 'success']);
    }

    public function daili(){

        $key = input('key');
        $map['groupid'] = 4;//0未删除，1已删除
        if($key&&$key!=="")
        {
            $map['mobile'] = ['like',"%" . $key . "%"];
        }
        $member = new AdminModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = 10;// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }

    public function add_time(){

        $id = input('member_id');
        $time = input('time');
        $start_time = time();
        $end_time = time() + $time * 86400;
        $result = Db::name('admin')->where(['id'=>$id])->update(['start_time'=>$start_time,'end_time'=>$end_time,'status'=>1]);
        if($result){
            return json(['code' => 1, 'data' => [], 'msg' => '开通成功']);
        }
    }
}