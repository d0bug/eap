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
	<h2>视频列表
		<span style="float:right;margin-right:300px" ><a href="<?php echo U('Vipschool/VipschoolCourse/courseContentManager',array('video'=>1,'course_id'=>$course_id))?>" class="f_14 blue">继续添加视频</a></span>
	</h2>
	<div id="list" class="clearfix">
		<?php if($videoList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="5%">ID</td>
				<td width="20%">视频名称</td>
				<td width="10%">是否允许试看</td>
				<td width="35%">视频文件ID</td>
				<td width="10%">知识点</td>
				<td width="10%">添加时间</td>
				<td width="10%">操作</td>
			</tr>
			<?php foreach($videoList as $key=>$video):?>
			<tr height=30>
				<td><?php echo $video['vid'];?></td>
				<td><?php echo $video['video_name'];?></td>
				<td><?php echo $video['allow_try'] == 0?'不允许':'允许：'.$video['try_time']."分钟";?></td>
				<td><?php echo $video['cc_vid'];?></td>
				<td><?php echo $video['knowlege_name'];?></td>
				<td><?php echo $video['instime'];?></td>
				<td>
					<a href="#" onclick="testMessageBox_viewCourse(event,'<?php echo U('Vipschool/vipschoolCourse/videoInfo',array('vid'=>$video['vid']))?>','视频信息')" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/updateVideo',array('vid'=>$video['vid'],'course_id'=>$video['course_id']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/deleteVideo',array('vid'=>$video['vid']))?>" class="blue">删除</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php  else:?>
		<div>暂无视频列表信息</div>
		<?php  endif;?>
	</div>
	<h2>讲义信息
		<span style="float:right;margin-right:300px" ><a href="<?php echo U('Vipschool/VipschoolCourse/courseContentManager',array('handout'=>1,'course_id'=>$course_id))?>" class="f_14 blue">继续添加讲义</a></span>
	</h2>
	<div id="list" class="clearfix">
		<?php if($handoutList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="5%">ID</td>
				<td width="20%">讲义名称</td>
				<td width="35%">讲义地址</td>
				<td width="10%">添加时间</td>
				<td width="10%">操作</td>
			</tr>
			<?php foreach($handoutList as $key=>$handout):?>
			<tr height=30>
				<td><?php echo $handout['hid'];?></td>
				<td><?php echo $handout['handout_name'];?></td>
				<td><?php echo $handout['handout_url']?></td>
				<td><?php echo $handout['instime']?></td>
				<td>
					<a href="#" onclick="testMessageBox_viewCourse(event,'<?php echo U('Vipschool/vipschoolCourse/handoutInfo',array('hid'=>$handout['hid']))?>','讲义信息')" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/updateHandout',array('hid'=>$handout['hid'],'course_id'=>$handout['course_id']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/deleteHandout',array('hid'=>$handout['hid']))?>" class="blue">删除</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php  else:?>
		<div>暂无讲义列表信息</div>
		<?php  endif;?>
	</div>
</div>
</div>
</body>
</html>