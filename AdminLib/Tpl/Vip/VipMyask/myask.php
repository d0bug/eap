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
		<form id="form1" name="form1" method="POST" action="/vip/vip_myask/myask">
	    <input type="hidden" name="askid" id="askid" value="<?php echo $askid;?>" />
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt" style="width:100px"><font color=red>*</font>请输入问题：</td>
				<td ><input type="text" name="keyword" id="keyword" value="<?php echo $keyword ;?>" placeholder="请输入问题..."  style="width:300px; height:25px"/>
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
	<hr>
	<div id="list" class="clearfix">
		<?php if($vipmyaskList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;序号</td>
				<td>标题</td>
				<td>提问者</td>
				<td>时间</td>
				<td>提问者IP</td>
				<td>访问量</td>
				<td>是否有效</td>
				<td>所属学部</td>
				<td>是否已回复</td>
				<td>操作</td>
			</tr>
			<?php foreach($vipmyaskList as $key=>$vipask):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_delete[]" id="id<?php echo $key?>" value="<?php echo $vipask['id'];?>">&nbsp;<?php echo $vipask['id'];?></td>
				<td><a href="/vip/vip_myask/vipaskreply/id/<?php echo $vipask['id']?>"><?php echo substr($vipask['title'],0,40);?></a></td>
				<td><?php echo $vipask['uname'];?></td>
				<td><?php echo date('Y-m-d H:i:s',$vipask['instime']);?></td>
				<td><?php echo $vipask['ip']; ?></td>
				<td><?php echo $vipask['visit_num']; ?></td>
				<td><?php if($vipask['status']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><?php if($vipask['grade']<=6):?>小学部<?php elseif($vipask['grade']>6 && $vipask['grade']<=9 ):?>初中部<?php else:?>高中部<?php endif;?></td>
				<td><?php if($vipask['is_reply']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
				<td><a href="#" onclick="testMessageBox_vipaskInfo(event,'<?php echo U('Vip/VipMyask/vipaskInfo',array('id'=>$vipask['id']))?>')">查看</a>&nbsp;&nbsp;
					<a href="/vip/vip_myask/vipaskreply/id/<?php echo $vipask['id']?>">回复</a>
					<!--<a href="javascript:void(0);" onclick="testMessageBox_MyaskInfo(event,'<?php echo $vipask['id']?>','<?php echo U('Vip/VipMyask/setMyaskStatus')?>',<?php echo $vipask['status']?>)">审核</a>-->
					<a href="javascript:void(0);" onclick="testMessageBox_MyaskInfo(event,'<?php echo U('Vip/VipMyask/setMyask',array('askid'=>$vipask['id']))?>')">审核</a>&nbsp;&nbsp;
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
function testMessageBox_vipaskInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:350px;\"><iframe src=\""+requestUrl+"\" width=\"600\" height=\"350\" style=\"border:0px\"></iframe></div>";
	showMessageBox('问题信息',messContent,objPos,630,0);
}
	
/*function testMessageBox_MyaskInfo(ev,id,requestUrl,status){
	var objPos = mousePosition(ev);
	var status = status ;
	var chk1 = '';
	var chk2 = '';
	
	if(id !=''){
		if(status == 1){
			chk1 = '';
			chk2 = 'checked';
		}
		if(status == 0){
			chk1 = 'checked';
			chk2 = '';		
		}

		messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\"><br><div class=\"center\">请选择该问题是否在APP前台显示？</div><br><div class=\"center\"><input type=radio id=status name=status value=\"1\" checked='"+chk1+"'>是　　　　　<input type=radio id=status name=status value=\"0\" checked='"+chk2+"'>否</div><br><div class=\"center\"><input type=button value=\"　确认　\"  onclick=\"reviewStatus('"+id+"','"+requestUrl+"')\"></div></div>";
		showMessageBox('设置是否显示',messContent,objPos,370,0);
	}else{
		alert('非法操作');
	}
}*/	

function testMessageBox_MyaskInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:200px;\"><iframe src=\""+requestUrl+"\" width=\"300\" height=\"200\" style=\"border:0px\"></iframe></div>";
	showMessageBox('对我的提问进行审核',messContent,objPos,330,0);
}


function reviewStatus(id,requestUrl){
	if(id !=''){
		$.get(requestUrl,
		{id:id,status:$("input[name=status]:checked").val(),date:new Date().toTimeString()},
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

</script>
</body>
</html>