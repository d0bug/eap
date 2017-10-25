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
	<?php if(!empty($courseInfo)):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">课程名称：</td>
			<td><?php echo $courseInfo['course_name']?></td>
		</tr>
		<tr>
			<td class="alt">关键字：</td>
			<td><?php echo $courseInfo['keywords']?$courseInfo['keywords']:'暂无关键字' ?></td>
		</tr>
		<tr>
			<td class="alt">描述：</td>
			<td ><?php echo $courseInfo['desc']?$courseInfo['desc']:'暂无描述' ?></td>
	    </tr>
	    <tr>
			<td class="alt">学部：</td>
			<td ><?php echo $courseInfo['grade']?$courseInfo['grade']:'暂无学部信息' ?></td>
	    </tr>
	    <tr>
			<td class="alt">学科：</td>
			<td ><?php echo $courseInfo['subject']?$courseInfo['subject']:'暂无学科信息' ?></td>
	    </tr>
	    <tr>
			<td class="alt">分类：</td>
			<td ><?php echo $courseInfo['classify']?$courseInfo['classify']:'暂无分类信息'; echo $courseInfo['twoclassify']?' > '.$courseInfo['twoclassify']:'';  echo $courseInfo['threeclassify']?' > '.$courseInfo['threeclassify']:'';echo $courseInfo['fourclassify']?' > '.$courseInfo['fourclassify']:'';?></td>
	    </tr>
	    <tr>
			<td class="alt">下线时间：</td>
			<td ><?php echo empty($courseInfo['endtime'])?'暂无下线时间':$courseInfo['endtime'] ?> 天</td>
	    </tr>
	    <tr>
			<td class="alt">价格：</td>
			<td ><?php echo $courseInfo['price']?$courseInfo['price']:'免费'?></td>
	    </tr>
	    <tr>
			<td class="alt">添加时间：</td>
			<td ><?php echo $courseInfo['instime']?></td>
	    </tr>
		 <tr>
			<td class="alt">最后修改时间：</td>
			<td ><?php echo $courseInfo['updtime']?$courseInfo['updtime']:"暂无修改" ?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>