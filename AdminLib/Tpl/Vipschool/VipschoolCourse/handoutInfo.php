<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div>
	<?php if(!empty($handoutInfo)):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">讲义名称：</td>
			<td><?php echo $handoutInfo['handout_name']?></td>
		</tr>
		<tr>
			<td class="alt">所属课程：</td>
			<td><?php echo $courseInfo['course_name']?></td>
		</tr>
		<tr>
			<td class="alt">讲义文件路径：</td>
			<td><?php echo $handoutInfo['handout_url']?$handoutInfo['handout_url']:'暂无文件路径' ?></td>
		</tr>
		<tr>
			<td class="alt">添加时间：</td>
			<td ><?php echo $handoutInfo['instime']?></td>
	    </tr>
		 <tr>
			<td class="alt">最后修改时间：</td>
			<td ><?php echo $handoutInfo['updtime']?$handoutInfo['updtime']:"暂无修改" ?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>