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
	<?php if(!empty($videoInfo)):?>
	<table width="900" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt" >视频名称：</td>
			<td><?php echo $videoInfo['video_name']?></td>
		</tr>
		<tr>
			<td class="alt">所属课程：</td>
			<td><?php echo $courseInfo['course_name']?></td>
		</tr>
		<tr>
			<td class="alt">是否允许试看：</td>
			<td><?php echo $videoInfo['allow_try'] == 0?'不允许':'允许：'.$videoInfo['try_time']."分钟";?></td>
		</tr>
		<tr>
			<td class="alt">视频文件ID：</td>
			<td ><?php echo $videoInfo['cc_vid']?$videoInfo['cc_vid']:'暂无视频' ?></td>
	    </tr>
	    <tr>
			<td class="alt">知识点：</td>
			<td ><?php echo $videoInfo['knowlege_name']; ?></td>
	    </tr>
	    <tr>
			<td class="alt">添加时间：</td>
			<td ><?php echo $videoInfo['instime']?></td>
	    </tr>
		 <tr>
			<td class="alt">最后修改时间：</td>
			<td ><?php echo $videoInfo['updtime']?$videoInfo['updtime']:"暂无修改" ?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>