<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
		<form >
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
		    </tr>
		    <tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		
		    	</td>
		    </tr>
		</table>
		</form>
	</div>
	<div id="search" style="text-align:right; margin:0 auto;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<td>
				   
			</td>
		</table>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($vipComplaintList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;ID号</td>
				<td>投拆者UID</td>
				<td>投拆者</td>
				<td width="50%">投拆内容</td>
				<td>时间</td>
                <td>是否回复</td>
                <td>回复</td>
			</tr>
			<?php foreach($vipComplaintList as $k=>$v):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['id'];?></td>
				<td><?php echo $v['uid'];?></td>
				<td><?php echo $v['uname'];?></td>
				<td width="50%"><?php echo $v['content'];?></a></td>
				<td><?php echo date('Y-m-d H:i:s',$v['instime']);?></td>
                <td><?php if($v['is_comment']==1):?><font color=green>是</font><?php else:?><font color="red">否</font><?php endif;?></td>
   	            <td><a href="/vip/VipCircle/vipComplainReply/cid/<?php echo $v['id']?>">回复</a></td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>