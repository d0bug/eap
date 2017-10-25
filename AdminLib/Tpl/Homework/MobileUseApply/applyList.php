<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
<style type="text/css">
body, h1, h2 {
	font-family: "Microsoft Yahei";
}
@media (min-width: 600px) {
.container {
	width: 95%;
}
}
h2 {
	border-bottom: 3px solid #EEEEEE;
	color: #575765;
	font-size: 24px;
	margin: 40px 0;
	padding-bottom: 10px;
}
</style>
<script>
jQuery(function($) {
	$(".say").click(function(){
		var id = $(this).attr('attr');
		var content = $("#say"+id).html();
		$("#say"+id).html('<input type="text" class="sayContent" qid="'+id+'" id="textsay'+id+'" name="text" value="'+content+'">');
	})
	$(".sayContent").live("blur",function(){
		var val = $(this).val();
		var id = $(this).attr('qid');
		$("#say"+id).html(val);
		$.post('/Homework/MobileUseApply/ajaxUpdateSay',{id:id,say:val});
	})
})
function agree(id){
	if(id>0){
		$.post('/Homework/MobileUseApply/ajaxUpdateMarking',{id:id,val:1},function(data){
			var arr  = $.parseJSON(data);
			if(arr['status'] == 1){
				$("#marking"+id).html('<div align="center" style="color:#0F3">已通过</div>');
			}	
			alert(arr['msg']);
		})
	}
}
function refuse(id){
	if(id>0){
		$.post('/Homework/MobileUseApply/ajaxUpdateMarking',{id:id,val:2},function(data){
			var arr  = $.parseJSON(data);
			if(arr['status'] == 1){
				$("#marking"+id).html('<div align="center" style="color:#F00">已拒绝</div>');
			}	
			alert(arr['msg']);
		})
	}
}
function goback(id,val){
	$.post('/Homework/MobileUseApply/ajaxUpdateGoBack',{id:id,val:val},function(data){
		if(data == 1){
			if(val == 1){
				$("#goback"+id).html("<div align='center'><a href='javascript:void(0);' style='color:#0F3' onclick='goback("+id+",0)'>已归还</a></div>");
			}else{
				$("#goback"+id).html("<div align='center'><a href='javascript:void(0);' style='color:#F00' onclick='goback("+id+",1)'>未归还</a></div>");
			}
			alert('修改成功');
		}else{
			alert('修改失败');
		}
	})
}

</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10" style="width:95%">
      <h2>申请列表</h2>
      <LABEL><a href="/homework/mobile_use_apply/applyList/type/0">只看未审批</a></LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
      <LABEL><a href="/homework/mobile_use_apply/applyList/type/1">只看未归还</a></LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
      <LABEL><a href="/homework/mobile_use_apply/applyList/">查看全部</a></LABEL>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($applyList)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
              	<th style="text-align:center">申请编号</th>
              	<th>申请人</th>
                <th>申请时间<br>使用时间<br>预计归还时间</th>
                <th>手机型号</th>
                <th>审批状态</th>
                <th>归还状态</th>
                <th>申请说明</th>
                <th>说点什么</th>
              </tr>
              <?php foreach($applyList as $item): ?>
              <tr id="{$item.id}">
              	<td>{$item.id}</td>
              	<td>{$item.applyname}</td>
                <td>申：<?php echo date('Y-m-d H:i',$item['createtime'])?><br>
                	借：<?php echo date('Y-m-d H:i',$item['starttime'])?><br>
                	还：<?php echo date('Y-m-d H:i',$item['endtime'])?>
                </td>
                <td>{$item['sname']}</td>
                <td id="marking{$item.id}">
                <?php
                if ($item['markingstatus'] == 1){
                	echo "<div align='center' style='color:#0F3'>已通过</div>";
                }elseif($item['markingstatus'] == 2){
                	echo "<div align='center' style='color:#F00'>已拒绝</div>";
                }else{
                	echo "<center><a href='javascript:void(0);' onclick=\"agree(".$item['id'].")\">同意</a> | <a href='javascript:void(0);' onclick=\"refuse(".$item['id'].")\">拒绝</a></center>";
                }
                ?>
                </td>
                <td id="goback{$item.id}">
	                	<?php
	                	if ($item['gobackstatus']==1){
	                		echo "<div align='center'><a href='javascript:void(0);' style='color:#0F3' onclick='goback(".$item['id'].",0)'>已归还</a></div>";
	                	}else{
	                		echo "<div align='center'><a href='javascript:void(0);' style='color:#F00' onclick='goback(".$item['id'].",1)'>未归还</a></div>";
	                	}
	                	?>
                </td>
                <td>{$item['contents']}</td>
                <td><div id="say{$item.id}">{$item['weilisay']}</div><a href="javascript:void(0)" class="say" attr="{$item.id}"><i class="glyphicon glyphicon-edit"></i></a></td>
              </tr>
              <?php endforeach; ?>
            </table>
            <div>{$show}</div>
            </div>
          <?php else: ?>
          <div class="col-md-10" style="background-color: #66CCFF; border: 1px solid #6699FF; color: #ffffff; font-size: 16px;width:95%">
            <div style="width: 200px; margin: 80px auto;">当前筛选条件下没有结果。</div>
          </div>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <div id="msg" class="col-md-offset-1 col-md-4 msg failed"></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="succeed" class="jGrowl top-center" style="background-color:#009900; color:#FFF;"></div>
<div id="failed" class="jGrowl top-center" style="background-color:#F60; color:#FFF;"></div>
</body>
</html>