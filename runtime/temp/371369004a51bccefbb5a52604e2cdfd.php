<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:61:"E:\NewProgram\yylm/application/otadmins\view\index\index.html";i:1604281471;s:63:"E:\NewProgram\yylm/application/otadmins\view\public\header.html";i:1604281471;s:63:"E:\NewProgram\yylm/application/otadmins\view\public\footer.html";i:1604281471;}*/ ?>
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



<div style="display:none;">
<!--此处为：CNZZ数据统计代码，请联系技术总监为客户注册，替换以下代码即可-->
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1253472858'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "v1.cnzz.com/stat.php%3Fid%3D1253472858%26online%3D1' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
<script>
<!--此处为：通过上述CNZZ数据统计代码，得到相应字段进行元素赋值-->
window.onload=function(){
	var num = $("#cnzz_stat_icon_1253472858").html();
	var data = /([^\[\]]+)(?=\])/g;
	data = num.match(data);
	
	var add = "<span style=\"font-size:14px\"> IP</span>"
	var dayip = data[0];
	var dayip2 = data[2];
	$("#dayip").html(dayip+add);
	$("#dayip2").html(dayip2+"IP");
	dayip = parseInt(dayip);
	dayip2 = parseInt(dayip2);
	if(dayip>dayip2){
			var daysum = dayip-dayip2;
			$("#dayip3").html("+"+daysum);
			$("#dayipclass").attr("class","fa fa-level-up");
		}else{
			var daysum = dayip2-dayip;
			$("#dayip3").html("-"+daysum);
			$("#dayipclass").attr("class","fa fa-level-down");
	}
	
	var add2 = "<span style=\"font-size:14px\"> PV</span>"
	var daypv = data[1];
	var daypv2 = data[3];
	$("#daypv").html(daypv+add2);
	$("#daypv2").html(daypv2+"PV");
	daypv = parseInt(daypv);
	daypv2 = parseInt(daypv2);
	if(daypv>daypv2){
			var daysum2 = daypv-daypv2;
			$("#daypv3").html("+ "+daysum2);
			$("#daypvclass").attr("class","fa fa-level-up");
		}else{
			var daysum2 = daypv2-daypv;
			$("#daypv3").html("- "+daysum2);
			$("#daypvclass").attr("class","fa fa-level-down");
	}
	
	var add3 = "<span style=\"font-size:14px\"> 人</span>"
	var online = data[4];
	$("#online").html(online+add3);

	
	

	}

</script>

<div class="wrapper wrapper-content">
    <!--<div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <div>尊敬的会员<span id="weather"></span></div>
    </div>-->

    <!-- 上方tab -->
    <div class="row">
        <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right">今日</span>
                        <h5>当前在线</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><span id="online">0<span style="font-size:14px"> 人</span></span></h1>
                        <div class="stat-percent font-bold text-success"><i class="fa fa-bolt"></i>
                        </div>
                        <small>在线人数统计</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-info pull-right">今日</span>
                        <h5>浏览次数（PV）</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><span id="daypv">0<span style="font-size:14px"> PV</span></span></h1>
                        <div class="stat-percent font-bold text-info">相比昨日 <span id="daypv3">0</span>PV <i class="fa" id="daypvclass"></i>
                        </div>
                        <small>昨日 <span id="daypv2">0PV</span></small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-danger pull-right">今日</span>
                        <h5>IP访问</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><span id="dayip">0<span style="font-size:14px"> IP</span></span></h1>
                        <div class="stat-percent font-bold text-danger">相比昨日 <span id="dayip3">0</span>IP <i class="fa" id="dayipclass"></i>
                        </div>
                        <small>昨日 <span id="dayip2">0IP</span></small>
                    </div>
                </div>
            </div>
            
            
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                   <span class="label label-primary pull-right" id="weather_cs"></span>
                    <h5>每日天气</h5>
                </div>
                <div class="ibox-content" id="weather_data">
                	<h1 class="no-margins">数据加载中...</h1><small>&nbsp;</small></div>
                </div>
            </div>
        </div>
        
        
        <div class="row">
        
        
        <div class="col-sm-8">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>热门文章</h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content" style="overflow:hidden;">
                                <table class="table table-hover no-margins">
                                    <thead>
                                        <tr>
                                            <th>标题</th>
                                            <th>点击数</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     <?php if(is_array($article_list) || $article_list instanceof \think\Collection || $article_list instanceof \think\Paginator): $i = 0; $__LIST__ = $article_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <tr>
                                            <td style="verflow:hidden;"><?php echo $vo['title']; ?><p style="color:#bbb"><i class="fa fa-clock-o"></i> <?php echo date('m-d',$vo['create_time']); ?></p></td>
                                            <td class="text-navy"> <i class="fa fa-level-up"></i> <?php echo $vo['views']; ?></td>
                                        </tr>
                                  	<?php endforeach; endif; else: echo "" ;endif; ?>
                                      
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>行为日志</h5>
                                
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                           
                            <div class="ibox-content">
                                <ul class="todo-list small-list ui-sortable">
                                  	<?php if(is_array($log_list) || $log_list instanceof \think\Collection || $log_list instanceof \think\Paginator): $i = 0; $__LIST__ = $log_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <li>
                                        <?php echo $vo['description']; ?><span class="stat-percent"><?php echo $vo['add_time']; ?></span>
                                    </li>
                                   <?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        
        
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>系统信息</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
              
                    <div class="ibox-content">
                        <div class="feed-activity-list">
							
                            <div class="feed-element">
                                <div>
                                  <small class="pull-right text-navy"></small>
                                    <strong>当前时间：</strong>
                                    <div id="weather_time"></div>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
							
                            
                    <div class="panel-body">
                        <p><i class="fa fa-sitemap"></i> 框架版本：ThinkPHP<?php echo $info['think_v']; ?></p>
                        <p><i class="fa fa-windows"></i> 服务环境：<?php echo $info['web_server']; ?></p>
                        <p><i class="fa fa-warning"></i> 上传附件限制：<?php echo $info['onload']; ?></p>
                        <p><i class="fa fa-credit-card"></i> PHP 版本：<?php echo $info['phpversion']; ?></p>
                    </div>

                         

                        </div>
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
<script src="/static/admin/js/jquery.leoweather.min.js"></script>

<script type="text/javascript">
	$('#weather_time').leoweather({format:'{年}年{月}月{日}日 星期{周} {时}:{分}:{秒}'});
	$('#weather_data').leoweather({format:'<h1 class="no-margins">{夜间气温}℃ ~ {白天气温}℃</h1><small>{天气} - {风向}（{风级}）</small><div class="stat-percent font-bold text-navy">{时段}</div>'});
	$('#weather_cs').leoweather({format:'{城市} - {天气}'});
</script>

</body>
</html>