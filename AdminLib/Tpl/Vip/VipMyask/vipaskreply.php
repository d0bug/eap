<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript">
	function checkForm(){	
		var content = $("#reply_content").val();
		if(content==''){
			alert('请输入回复内容！');
			return false;
		}
		var askid = $("#askid").val();
		var url = '/vip/vip_myask/replycontent';
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{content:content,askid:askid},
		    dataType: 'json',
		    success:function(data) {    
		        if(data.status ==1 ){      
		            alert(data.msg); 
		            window.location.reload(); 
		             $('#reply_content').val('');     
		        }else{    
		            alert(data.msg);
		            $('#reply_content').val('');      
		        }    
	    	},
		    error : function() {
		          alert("异常！");    
		     }    
		});
	}
</script>
<script type="text/javascript">
function testMessageBox_replyInfo(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	if(type == 'add'){
		title = '添加回复';
	}
	if(type == 'edit'){
		title = '编辑回复';
	}

	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:500px;\"><iframe src=\""+requestUrl+"\" width=\"750\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox(title,messContent,objPos,780,0);
}


function Del_ReplyInfo(rid,askid){
		if(!confirm("您确定要删除此内容吗？删除将无法回复")){
			 return false;
		}
		var rid = rid;		
		var url = '/vip/vip_myask/DelReply';
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{rid:rid,askid},
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
</head>
<body>
<div region="center">
<div id="main">
	<h1><?php echo $one_askinfo;?>  &nbsp;&nbsp;<a href="/vip/vip_myask/myask">返回列表</a></h1>
		<form id="form1" name="form1" method="post">
		    <input type="hidden" name="askid" id="askid" value="<?php echo $askid;?>" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr>
					<td class="alt" style="width:50px"><font color=red>*</font>回复：</td>
					<td><textarea name="reply_content" id="reply_content" style="width:60%;height:400px" placeholder="输入资讯内容..."></textarea><span class="error"></span>
			        </td>
			    </tr>
			    <tr>
			   		<td style="text-align:center; margin:0 auto;">
			     		<input type="submit" class="btn"  value="提交" onclick="checkForm();return false;">
			    	</td>
			    </tr>
			</table>
		</form>
	<hr>
	<div id="list" class="clearfix">
		<?php if($replyListInfo):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;序号</td>
				<td>回复内容</td>
				<td>回复者</td>
				<td>回复时间</td>
				<td>回复IP</td>
				<td>是否有效</td>
				<td>操作</td>
			</tr>
			<?php foreach($replyListInfo as $key=>$reply):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_delete[]" id="id<?php echo $key?>" value="<?php echo $reply['id'];?>">&nbsp;<?php echo $reply['id'];?></td>
				<td><?php echo $reply['content'];?></td>
				<td><?php echo $reply['reply_uname'];?></td>
				<td><?php echo date('Y-m-d H:i:s',$reply['instime']);?></td>
				<td><?php echo $reply['ip'];?></td>
				<td><?php if($reply['status'] == 1) echo '是'; else echo '否';?></td>
				<td><a href="javascript:void(0);" onclick="testMessageBox_replyInfo(event,'edit','<?php echo U('Vip/vip_myask/replyOperate',array('id'=>$reply['id']));?>')">编辑</a></td>
				<td><a href="javascript:void(0);" onclick="Del_ReplyInfo('<?php echo $reply['id']?>','<?php echo $reply['askid']?>')">删除</a></td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkAll" id="checkAll" value="1" >全选&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无回复信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>