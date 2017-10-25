<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center" >
<div id="main">
		<table class="tableInfo" width="80%">
			<tr>
				<td align="right" width="30%">问题：</td>
				<td><?php echo $askInfo['title']?></td>
			</tr>
			<tr>
				<td align="right">用户UID：</td>
				<td><?php echo $askInfo['uid']?></td>
			</tr>
			<tr>
				<td align="right">用户name：</td>
				<td><?php echo $askInfo['uname']?></td>
			</tr>

			<tr>
				<td align="right">用户IP：</td>
				<td><?php echo $askInfo['ip']?></td>
			</tr>

			<tr>
				<td align="right">用户访问量：</td>
				<td><?php echo $askInfo['visit_num']?></td>
			</tr>

			<tr>
				<td align="right">是否回复：</td>
				<td><?php if($askInfo['is_reply'] == 1) echo '是'; else echo '否';?></td>
			</tr>		
			<tr>
				<td align="right">是否有效：</td>
				<td><?php if($askInfo['status'] == 1) echo '是'; else echo '否';?></td>
			</tr>
			<tr>
				<td align="right">年部：</td>
				<td><?php if($askInfo['grade'] >= 1 && $askInfo['grade'] <=6) echo '小学部'; else if($askInfo['grade'] >=7 && $askInfo['grade'] <=9 ) echo '初中部'; else echo '高中部';?></td>
			</tr>					
		</table>
</div>
</div>
</body>
</html>