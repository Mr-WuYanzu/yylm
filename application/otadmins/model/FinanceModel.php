<?php

namespace app\otadmins\model;
use think\Model;
use think\Db;

class FinanceModel extends Model
{
    protected $name = 'earnings';
    
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;


    /**
     * 根据搜索条件获取用户列表信息
     * @author [OUTENG欧腾]
     */
    public function getFinanceByWhere($map, $Nowpage, $limits)
    {
        return $this->alias('e')->join('think_member m','e.uid = m.id')->field('e.*,m.nickname')->where($map)->page($Nowpage, $limits)->order('add_time desc')->select();
    }
    
    
//    /**
//     * [insertArticle 添加文章]
//     * @author [OUTENG欧腾]
//     */
//    public function insertFinance($param)
//    {
//        try{
//            $result = $this->allowField(true)->save($param);
//            if(false === $result){
//                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
//            }else{
//                return ['code' => 1, 'data' => '', 'msg' => '文章添加成功'];
//            }
//        }catch( PDOException $e){
//            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
//        }
//    }
//
//
//
//    /**
//     * [updateArticle 编辑文章]
//     * @author [OUTENG欧腾]
//     */
//    public function updateArticle($param)
//    {
//        try{
//            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
//            if(false === $result){
//                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
//            }else{
//                return ['code' => 1, 'data' => '', 'msg' => '文章编辑成功'];
//            }
//        }catch( PDOException $e){
//            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
//        }
//    }



    /**
     * [getOneArticle 根据文章id获取一条信息]
     * @author [OUTENG欧腾]
     */
    public function getOneArticle($id)
    {
        return $this->where('id', $id)->find();
    }



    /**
     * [delArticle 删除文章]
     * @author [OUTENG欧腾]
     */
    public function delArticle($id)
    {
        try{
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '文章删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}