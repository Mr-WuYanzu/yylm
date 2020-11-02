<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:72:"E:\WWWROOT\outengcms\WwwRoot/application/otadmins\view\config\index.html";i:1485218644;s:73:"E:\WWWROOT\outengcms\WwwRoot/application/otadmins\view\public\header.html";i:1484102488;s:73:"E:\WWWROOT\outengcms\WwwRoot/application/otadmins\view\public\footer.html";i:1494225877;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo config('WEB_SITE_TITLE'); ?></title>
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/switchery/switchery.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style type="text/css">
    .long-tr th{
        text-align: center
    }
    .long-td td{
        text-align: center
    }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>配置列表</h5>
        </div>
        <div class="ibox-content">
            <!--搜索框开始-->           
            <div class="row">
                <div class="col-sm-12">   
                <div  class="col-sm-2" style="width: 100px">
                    <div class="input-group" >  
                        <a href="<?php echo url('add_config'); ?>"><button class="btn btn-outline btn-primary" type="button">添加配置</button></a> 
                    </div>
                </div>                                            
                    <form name="admin_list_sea" class="form-search" method="post" action="<?php echo url('index'); ?>">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" id="key" class="form-control" name="key" value="<?php echo $val; ?>" placeholder="输入需查询的标题" />   
                                <span class="input-group-btn"> 
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button> 
                                </span>
                            </div>
                        </div>
                    </form>                         
                </div>
            </div>
            <!--搜索框结束-->
            <div class="hr-line-dashed"></div>

            <div class="example-wrap">
                <div class="example">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="long-tr">
                                <th>ID</th>
                                <th>名称</th>
                                <th>标题</th>
                                <th>类型</th>
                                <th>分组</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                                <tr class="long-td">
                                    <td><?php echo $vo['id']; ?></td>
                                    <td><?php echo $vo['name']; ?></td>
                                    <td><?php echo $vo['title']; ?></td>                                  
                                    <td><?php echo get_config_type($vo['type']); ?></td>
                                    <td><?php echo get_config_group($vo['group']); ?></td>
                                    <td><?php echo $vo['create_time']; ?></td>
                                    <td><?php echo $vo['update_time']; ?></td>                               
                                    <td>
                                        <?php if($vo['status'] == 1): ?>
                                            <a class="red" href="javascript:;" onclick="status_config(<?php echo $vo['id']; ?>);">
                                                <div id="zt<?php echo $vo['id']; ?>"><span class="label label-info">开启</span></div>
                                            </a>
                                        <?php else: ?>
                                            <a class="red" href="javascript:;" onclick="status_config(<?php echo $vo['id']; ?>);">
                                                <div id="zt<?php echo $vo['id']; ?>"><span class="label label-danger">禁用</span></div>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo url('Config/edit_config',['id'=>$vo['id']]); ?>" class="btn btn-primary btn-xs">
                                            <i class="fa fa-paste"></i> 编辑</a>&nbsp;&nbsp;
                                        <a href="javascript:;" onclick="del_config(<?php echo $vo['id']; ?>)" class="btn btn-danger btn-xs">
                                            <i class="fa fa-trash-o"></i> 删除</a> 
                                    </td>
                                </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>                      
                        </tbody>
                    </table>
                    <div id="Pages" style="text-align:right;"></div><div id="allpage" style=" text-align: right;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/bootstrap.min.js?v=3.3.6"></script>
<script src="__JS__/content.min.js?v=1.0.0"></script>
<script src="__JS__/plugins/chosen/chosen.jquery.js"></script>
<script src="__JS__/plugins/iCheck/icheck.min.js"></script>
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/switchery/switchery.js"></script><!--IOS开关样式-->
<script src="__JS__/jquery.form.js"></script>
<script src="__JS__/layer/layer.js"></script>
<script src="__JS__/laypage/laypage.js"></script>
<script src="__JS__/laytpl/laytpl.js"></script>
<script src="__JS__/otcms.js"></script>
<script>
    $(document).ready(function(){$(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",})});
</script>

<script type="text/javascript">
   
//分页
laypage({
    cont: $('#Pages'),  // 分页容器
    pages: "<?php echo $allpage; ?>",// 总页数
    skip: false, //是否开启跳页
    skin: '#1AB5B7',//分页组件颜色
    groups: 3,//连续显示分页数
    curr: function(){
        var page = "<?php echo $nowpage; ?>"; // 当前页(后台获取到的)
        return page ? page : 1; // 返回当前页码值
    }(),
    jump: function(e, first){ //触发分页后的回调(单击页码后)
        if(!first){ //一定要加此判断，否则初始时会无限刷新
            location.href = '?page='+e.curr;
        }
    }
});


/**
 * [del_config 删除配置]
 * @Author[田建龙 864491238@qq.com]
 * @param   {[type]}    id [配置id]
 */
function del_config(id){
    layer.confirm('确认删除此配置?', {icon: 3, title:'提示'}, function(index){
        //do something
        $.getJSON('./del_config', {'id' : id}, function(res){
            if(res.code == 1){
                layer.msg(res.msg,{icon:1,time:1500});
                Ajaxpage(1,5)
            }else{
                layer.msg(res.msg,{icon:0,time:1500});
            }
        });

        layer.close(index);
    })

}

/**
 * [status_config 配置状态]
 * @param  {[type]} val [description]
 * @Author[田建龙 864491238@qq.com]
 */
function status_config(val){
    $.post('<?php echo url("status_config"); ?>',
    {id:val},
    function(data){
         
        if(data.code==1){
            var a='<span class="label label-danger">禁用</span>'
            $('#zt'+val).html(a);
            layer.msg(data.msg,{icon:2,time:1500,shade: 0.1,});
            return false;
        }else{
            var b='<span class="label label-info">开启</span>'
            $('#zt'+val).html(b);
            layer.msg(data.msg,{icon:1,time:1500,shade: 0.1,});
            return false;
        }         
        
    });
    return false;
}


</script>
</body>
</html>