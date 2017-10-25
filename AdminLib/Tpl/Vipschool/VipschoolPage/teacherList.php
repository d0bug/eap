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
	<h2>教师管理<span style="float:right;margin-right:100px" ><a href="<?php echo U('Vipschool/VipschoolPage/addTeacher')?>" class="f_14 blue">添加教师</a></span></h2>
	<div id="search">
		<form id="searchForm" method="POST" action="">
		教师名称：<input type="text" id="keyword" name="keyword" value="<?php echo urldecode($keyword);?>" size="30" placeholder="请输入教师姓名">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="搜索" class="btn2">
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($teacherList):?>
		<table width="100%" class="tableInfo">
			<tr bgcolor="#dddddd" height=35>
				<td>ID</td>
				<td>教师姓名</td>
				<td>主讲年级</td>
				<td>主讲学科</td>
				<td>教师寄语</td>
				<td>添加时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($teacherList as $key=>$teacher):?>
			<tr height=30>
				<td><?php echo $teacher['tid'];?></td>
				<td><?php echo $teacher['realname'];?></td>
				<td><?php echo $teacher['grade'];?></td>
				<td><?php echo $teacher['subject'];?></td>
				<td><?php echo $teacher['send_word'];?></td>
				<td><?php echo $teacher['instime'];?></td>
				<td>
					<a href="#" onclick="testMessageBox_viewTeacher(event,'<?php echo U('Vipschool/VipschoolPage/teacherInfo',array('tid'=>$teacher['tid']))?>','<?php echo U('Vipschool/VipschoolPage/updateTeacher',array('tid'=>$teacher['tid']))?>')" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<!--<a href="<?php echo U('Vipschool/VipschoolPage/teacherInfo',array('tid'=>$teacher['tid']))?>" class="blue" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;-->
					<a href="<?php echo U('Vipschool/VipschoolPage/updateTeacher',array('tid'=>$teacher['tid']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if($teacher['is_recommend']==1):?>
						<span class="gray">已推荐</span>
					<?php else:?>
						<a href="#" class="blue" onclick="recommend_teacher(<?php echo $teacher['tid']?>,'<?php echo U('Vipschool/VipschoolPage/recommendTeacher')?>')">推荐</a>
					<?php endif;?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if($teacher['is_onjob']==0):?>
						<span class="gray">已离职</span>
					<?php else:?>
						<a href="#" class="blue" onclick="dimission_teacher(<?php echo $teacher['tid']?>,'<?php echo U('Vipschool/VipschoolPage/dimissionTeacher')?>')">离职</a>
					<?php endif;?>
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