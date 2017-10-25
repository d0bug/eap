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
				<td><?php echo $CircleInfo['title']?></td>
			</tr>
			<tr>
				<td align="right" width="30%">介绍：</td>
				<td><?php echo stripslashes($CircleInfo['intro'])?></td>
			</tr>			
			<tr>
				<td align="right">用户UID：</td>
				<td><?php echo $CircleInfo['uid']?></td>
			</tr>
			<tr>
				<td align="right">用户name：</td>
				<td><?php echo $CircleInfo['uname']?></td>
			</tr>

			<tr>
				<td align="right">用户IP：</td>
				<td><?php echo $CircleInfo['ip']?></td>
			</tr>

			<tr>
				<td align="right">用户访问量：</td>
				<td><?php echo $CircleInfo['reading_num']?></td>
			</tr>

			<tr>
				<td align="right">是否评论：</td>
				<td><?php if($CircleInfo['is_comment'] == 1) echo '是'; else echo '否';?></td>
			</tr>		
			<tr>
				<td align="right">是否有效：</td>
				<td><?php if($CircleInfo['status'] == 1) echo '是'; else echo '否';?></td>
			</tr>
			<tr>
				<td align="right">是否置顶：</td>
				<td><?php if($CircleInfo['is_top'] == 1) echo '是'; else echo '否';?></td>
			</tr>
			<tr>
				<td align="right">是否推荐：</td>
				<td><?php if($CircleInfo['is_recommend'] == 1) echo '是'; else echo '否';?></td>
			</tr>								
		</table>
</div>
</div>
</body>
</html>