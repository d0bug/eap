<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<table border="0">
		<tr>
			<td><img src="/static/images/default_avatar.jpg" width="80" height="80"></td>
			<td valign="top">
				<p>&nbsp;&nbsp;<font class="f_20"><?php echo $studentInfo['sstudentname']?></font>&nbsp;|&nbsp;<?php echo $studentInfo['sschool']?></p>
				<p>&nbsp;&nbsp;<?php echo $studentInfo['gradename']?></p>
			</td>
		</tr>
	</table><br>
	<div class="tableTab">
		<ul class="tab">
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$student_code))?>">学员课程</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$student_code))?>">辅导方案</a>
			</li>
			<li class="current">
				<a href="<?php echo U('Vip/VipStudents/newStudentMessage',array('student_code'=>$student_code))?>">留言板</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentInfo',array('student_code'=>$student_code))?>">学员详情</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentErrorQuestion',array('student_code'=>$student_code))?>">错题书包</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$student_code))?>">课程规划</a>
			</li>
		</ul>
	</div><br/>
	<?php if(!empty($heluInfo['comment'])):?>
		<p>
			<span class="blue"><?php echo $userInfo['real_name'];?>老师：</span> <?php echo $heluInfo['comment'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="gray"><?php echo $heluInfo['lasttime'];?></span>
		</p>
	<?php else:?>
		<span class="gray">暂无留言~</span>
	<?php endif;?>
</div>
</div>
</body>
</html>