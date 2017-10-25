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
	<h2>打包列表<span style="float:right;margin-right:100px" ><a href="<?php echo U('Vipschool/VipschoolCourse/addCoursePack')?>" class="f_14 blue">添加打包</a></span></h2>
	<div id="search">
		<form id="searchForm" method="POST" action="<?php echo U('Vipschool/VipschoolCourse/packCourseManage');?>">
		课程包名称：<input type="text" id="pname" name="pname" value="<?php echo urldecode($pname);?>" size="30" placeholder="请输入关键词">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索" class="btn2">
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php  if($packList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="50">编号</td>
				<td width="200">打包名称</td>
				<td width="110">课程类型</td>
				<td width="110">生成时间</td>
				<td width="110">包含课程</td>
				<td>操作</td>
			</tr>
			<?php foreach($packList as $key=>$pack):?>
			<tr height=30>
				<td><?php echo $pack['pid'];?></td>
				<td><?php echo $pack['pname'];?></td>
				<td><?php echo $pack['ptype'] == 0?'课程已完':'课程预售';?></td>
				<td><?php echo $pack['instime'];?></td>
				<td><?php echo count(explode(',',$pack['course_id_str']))?count(explode(',',$pack['course_id_str'])):'暂无课程'?></td>
				<td>
					<a href="#" onclick="testMessageBox_viewCourse(event,'<?php echo U('Vipschool/vipschoolCourse/packInfo',array('pid'=>$pack['pid']))?>','打包课程信息')" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/vipschoolCourse/updatePack',array('pid'=>$pack['pid']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php   else:?>
		<div>暂无相关信息</div>
		<?php  endif;?>
	</div>

</div>
</div>
</body>
</html>