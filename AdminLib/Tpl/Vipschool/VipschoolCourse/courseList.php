<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/courseClassify.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>课程列表<span style="float:right;margin-right:100px" ><a href="<?php echo U('Vipschool/VipschoolCourse/uploadCourse')?>" class="f_14 blue">上传课程</a></span></h2>
	<div id="search">
		<form id="searchForm" method="POST" action="<?php echo U('Vipschool/VipschoolCourse/courseList');?>">
		课程名称：<input type="text" id="course_name" name="course_name" value="<?php echo urldecode($course_name);?>" size="30" placeholder="请输入关键词">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索" class="btn2">
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($courseList):?>
		
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="50">ID</td>
				<td width="350">课程名称</td>
				<td width="150">视频数量</td>
				<td width="150">讲义数量</td>
				<td width="180">添加时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($courseList as $key=>$course):?>
			<tr height=30>
				<td><?php echo $course['id'];?></td>
				<td><?php echo $course['course_name'];?></td>
				<td><?php echo $videoNum[$course['id']]?$videoNum[$course['id']]:0;?></td>
				<td><?php echo $handoutNum[$course['id']]?$handoutNum[$course['id']]:0;?></td>
				<td><?php echo $course['instime'];?></td>
				<td>
					<a href="#" onclick="testMessageBox_viewCourse(event,'<?php echo U('Vipschool/vipschoolCourse/courseInfo',array('id'=>$course['id']))?>','课程信息')" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/updateCourse',array('id'=>$course['id']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
				 	<a href="<?php echo U('Vipschool/vipschoolCourse/videoManage',array('id'=>$course['id']))?>" class="blue" >视频讲义管理</a>&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php  else:?>
		<div>暂无相关信息</div>
		<?php  endif;?>
	</div>

</div>
</div>
</body>
</html>