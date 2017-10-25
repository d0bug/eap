<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2><a href="/vip/vip_circle/VipComplaintList">返回列表</a></h2>
    <h4 style="color: red;">问题：<?php echo $one_Circleinfo;?>  &nbsp;&nbsp;</h4>
		<form id="form1" name="form1">
	    <input type="hidden" name="cid" id="cid" value="<?php echo $cid;?>" />
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt" style="width:50px"><font color=red>*</font>回复：</td>
				<td><textarea name="comment_content" id="comment_content" style="width:60%;height:400px" placeholder="输入评论内容..."></textarea><span class="error"></span>
		        </td>
		    </tr>
		    <tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		<input type="button" value="提交" onclick="return check();">
		    	</td>
		    </tr>
		</table>
		</form>
	<hr>
	<div id="list" class="clearfix">
		<?php if($ReplyCircleList):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;序号</td>
				<td>回复内容</td>
				<td>回复者UID</td>
				<td>回复者</td>
				<td>回复时间</td>
			</tr>
			<?php foreach($ReplyCircleList as $key=>$CirRep):?>
			<tr height=30>
				<!--td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_delete[]" id="id<?php echo $key?>" value="<?php echo $CirRep['id'];?>">&nbsp;<?php echo $CirRep['id'];?></td-->
				<td>&nbsp;&nbsp;&nbsp;<?php echo $CirRep['id'];?></td>
                <td><?php echo $CirRep['ucontent'];?></td>
				<td><?php echo $CirRep['ucode'];?></td>
				<td><?php echo $CirRep['uname'];?></td>
				<td><?php echo date('Y-m-d H:i:s',$CirRep['instime']);?></td>
			
				<!--td><a href="javascript:void(0);" onclick="Del_CircleReplyInfo('<?php echo $CirRep['id']?>')">删除</a><br/></td-->
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无回复信息</div>
		<?php endif;?>
	</div>
</div>
</div>
<script type="text/javascript">
	function check(){	
		var content = $("#comment_content").val();
		if(content==''){
			alert('请输入评论内容！');
			return false;
		}
		var cid = $("#cid").val();
		var url = '/vip/vipCircle/Complain_content';
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{content:content,cid:cid},
		    dataType: 'json',
		    success:function(data) {    
		        if(data.status ==1 ){      
		            alert(data.msg); 
		            window.location.reload(); 
		             $('#comment_content').val('');     
		        }else{    
		            alert(data.msg);
		            $('#comment_content').val('');      
		        }    
	    	},
		    error : function() {
		          alert("异常！");    
		     }    
		});
	}
/*
function Del_CircleReplyInfo(rid){
		if(!confirm("您确定要删除此回复吗？")){
			 return false;
		}
		var rid = rid;
		var url = '/vip/vip_circle/DelComplainReply'
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{rid:rid},
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
*/
</script>
</body>
</html>