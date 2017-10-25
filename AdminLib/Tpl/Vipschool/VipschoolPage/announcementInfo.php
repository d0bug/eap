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
	<div id="main"><h2>活动公告详情</h2></div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt"><font color=red>*</font>活动标题：</td>
			<td><?php echo $announcementInfo['title']?></td>
		</tr>
		
		<tr><td class="alt"><font color=red>*</font>活动图片：</td>
			<td>
				<img src="<?php echo $announcementInfo['show_img']?>" width="80%">
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>关键词：</td>
			<td><?php echo $announcementInfo['keywords']?></td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>活动描述：</td>
			<td><?php echo $announcementInfo['description']?></td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>活动内容：</td>
			<td ><div style="width:1200px;overflow: hidden"><?php echo $announcementInfo['content']?></div></td>
	    </tr>
		<tr><td>&nbsp;</td>
			<td><a href="<?php echo U('Vipschool/VipschoolPage/updateAnnouncement',array('aid'=>$aid))?>" class="blue">修改</a></td>
		</tr>
	</table>
</div>
</div>
</body>
</html>