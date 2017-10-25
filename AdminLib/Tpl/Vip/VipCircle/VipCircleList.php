<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
		<form id="form1" name="form1" method="POST" action="/vip/VipCircle/VipCircleList">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt" style="width:150px"><font color=red>*</font>请输入圈子标题：</td>
				<td ><input type="text" name="keyword" id="keyword" value="<?php echo $keyword ;?>" placeholder="请输入圈子标题..."  style="width:300px; height:25px"/>
		        </td>
		    </tr>
		    <tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		<input type="submit" value="搜索">
		    	</td>
		    </tr>
		</table>
		</form>
	</div>
	<div id="search" style="text-align:right; margin:0 auto;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<td>
				   <input type="submit" value="添加圈子" onclick="testMessageBox_CircleInfo(event,'add','<?php echo U('Vip/VipCircle/CircleOperate')?>')">
			</td>
		</table>
	</div>
	<hr>

	<div id="list" class="clearfix">
		<?php if($vipCircleList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;序号</td>
				<td>标题</td>
				<td>建圈者</td>
				<td>时间</td>
				<td>建圈者IP</td>
				<td>访问量</td>
				<td>是否有效</td>
				<td>是否置顶</td>
				<td>是否推荐</td>
				<td>是否已评论</td>
				<td>操作</td>
			</tr>
			<?php foreach($vipCircleList as $key=>$circle):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_delete[]" id="id<?php echo $key?>" value="<?php echo $circle['id'];?>">&nbsp;<?php echo $circle['id'];?></td>
				<td><a href="/vip/VipCircle/vipCircleReply/cid/<?php echo $circle['id']?>"><?php echo substr($circle['title'],0,40);?></a></td>
				<td><?php echo $circle['uname'];?></td>
				<td><?php echo date('Y-m-d H:i:s',$circle['instime']);?></td>
				<td><?php echo $circle['ip']; ?></td>
				<td><?php echo $circle['reading_num']; ?></td>
				<td><?php if($circle['status']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><?php if($circle['is_top']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><?php if($circle['is_recommend']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><?php if($circle['is_comment']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><a href="#" onclick="testMessageBox_vipcircleInfo(event,'<?php echo U('Vip/VipCircle/vipCircleInfo',array('id'=>$circle['id']))?>')">查看</a>&nbsp;&nbsp;
					<a href="/vip/VipCircle/vipCircleReply/cid/<?php echo $circle['id']?>">评论</a>
					<a href="javascript:void(0);" onclick="testMessageBox_CircleStatus(event,'<?php echo U('Vip/VipCircle/setCircle',array('cid'=>$circle['id']))?>')">设置</a>&nbsp;&nbsp;
					<a href="javascript:void(0);" onclick="testMessageBox_CircleInfo(event,'edit','<?php echo U('Vip/VipCircle/CircleOperate',array('id'=>$circle['id']))?>')">编辑</a>
					<a href="javascript:void(0);" onclick="Del_CircleInfo('<?php echo $circle['id']?>')">删除</a><br/>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkAll" id="checkAll" value="1" >全选&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
<script type="text/javascript">

$(function () {
	$("#checkAll").click(function () {
		if(this.checked){
			$("input[name='is_delete[]']:checkbox").attr("checked", true);
		}else{
			$("input[name='is_delete[]']:checkbox").attr("checked", false);
		}
	});
});

function testMessageBox_CircleInfo(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	if(type == 'add'){
		title = '添加圈子';
	}
	if(type == 'edit'){
		title = '编辑圈子';
	}

	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:500px;\"><iframe src=\""+requestUrl+"\" width=\"750\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox(title,messContent,objPos,780,0);
}

function testMessageBox_CircleStatus(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:500px;\"><iframe src=\""+requestUrl+"\" width=\"600\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox('对圈子进行设置',messContent,objPos,630,0);
}
function testMessageBox_vipcircleInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:350px;\"><iframe src=\""+requestUrl+"\" width=\"600\" height=\"350\" style=\"border:0px\"></iframe></div>";
	showMessageBox('问题信息',messContent,objPos,630,0);
}

function testMessageBox_CircleStatusInfo(id,requestUrl){
	if(id !=''){
		$.get(requestUrl,
		{cid:id,status:$("input[name=status]:checked").val(),date:new Date().toTimeString()},
		function(data){
			if(data=='0'){
				alert('设置失败');
				window.location.reload();
			}else{
				alert('设置成功');
				window.location.reload();
			}
		}
		);
	}else{
		alert('非法操作');
	}
}


function Del_CircleInfo(cid){
		if(!confirm("您确定要删除此圈子吗？删除将无法回复且对应的评论也将删除！")){
			 return false;
		}
		var cid = cid;		
		var url = '/vip/vip_circle/DelCircle';
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{cid:cid},
		    dataType: 'json',
		    success:function(data) {    
		        if(data.status ==1 ){      
		            alert(data.msg); 
		            window.location.reload();   
		        }else if(data.status==0 || data.status==2){    
		            alert(data.msg);  
		        } else{
		        	alert('出现错误！');
		        }   
	    	},
		    error : function() {
		          alert("异常！");    
		     }    
		});
}
</script>
</body>
</html>