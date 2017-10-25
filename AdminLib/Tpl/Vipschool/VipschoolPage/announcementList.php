<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>活动公告管理<span style="float:right;margin-right:100px" ><a href="<?php echo U('Vipschool/VipschoolPage/addAnnouncement')?>" class="f_14 blue">添加公告</a></span></h2>
	<div id="search">
		<form id="searchForm" method="POST" action="">
		活动名称：<input type="text" id="keyword" name="keyword" value="<?php echo urldecode($keyword);?>" size="40" placeholder="请输入关键词">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索" class="btn2">
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($announcementList):?>
		<table width="100%" class="tableInfo">
			<tr bgcolor="#dddddd" height=35>
				<td>ID</td>
				<td>活动名称</td>
				<td>添加时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($announcementList as $key=>$announcement):?>
			<tr height=30>
				<td><?php echo $announcement['id'];?></td>
				<td><?php echo $announcement['title'];?></td>
				<td><?php echo $announcement['instime'];?></td>
				<td>
					<!--<a href="#" class="blue" onclick="testMessageBox_viewAnnouncement(event,'<?php echo U('Vipschool/VipschoolPage/announcementInfo',array('aid'=>$announcement['id']))?>')">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" class="blue" onclick="testMessageBox_editAnnouncement(event,'<?php echo U('Vipschool/VipschoolPage/updateAnnouncement',array('aid'=>$announcement['id']))?>')">编辑</a>-->
					<a href="<?php echo U('Vipschool/VipschoolPage/announcementInfo',array('aid'=>$announcement['id']))?>" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/VipschoolPage/updateAnnouncement',array('aid'=>$announcement['id']))?>" class="blue" >编辑</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>