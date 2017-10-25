<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/popup2.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
.tableInfo td { border: 1px solid #ddd; color: #666; padding: 10px 5px;}
</style>
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
	<div id="search">
	<div class="tableTab">
		<ul class="tab">
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$student_code))?>">学员课程</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$student_code))?>">辅导方案</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentMessage',array('student_code'=>$student_code))?>">留言板</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentInfo',array('student_code'=>$student_code))?>">学员详情</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentErrorQuestion',array('student_code'=>$student_code))?>">错题书包</a>
			</li>
			<li class="current">
				<a href="<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$student_code))?>">课程规划</a>
			</li>
		</ul>
	</div>
	</div><br/>
		<div class="programForm">
			<form method="GET" action="<?php echo U('Vip/VipStudents/addVipProgram',array('student_code'=>$student_code))?>">
<!-- 				<div class="h40">课程名称：<?php echo $testCoach['subjectname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;生成截止日期：<?php echo $testCoach['dtauditenddate'];?></div> -->
<!-- 					<div class="h40">
					上课日期：<input type="text" id="start" name="start" value="" class="Wdate" placeholder="开始日期" onClick="WdatePicker()"> 
						  至 <input type="text" id="end" name="end" value="" class="Wdate" placeholder="截止日期" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(start)}'});"></div> -->
				<div class="h40">
					<input type="submit" value="添加课程规划" >
				</div>
			</form>
		</div>
	<br>
	<div id="programList">
		<table border="1" width="80%">
			<tr>
				<th align="center">生成时间</th>
				<th align="center">修改时间</th>
				<th align="center">课程名称</th>
				<th align="center">操作</th>
			</tr>
					<?php if(!empty($programList)):?>
						<?php foreach($programList as $key=>$program):?>
							<tr id="p_<?php echo $program['addtime'];?>">
								<td ><?php echo $program['addtime'];?></td>
								<td ><?php echo $program['updatetime'];?></td>
								<td ><?php echo $program['kecheng_name'];?></td>
								<td align="center">
									<a href="<?php echo U('Vip/VipStudents/showVipProgram',array('id'=>$program['id']))?>" class="blue" target="_blank">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<a href="<?php echo U('Vip/VipStudents/editVipProgram',array('id'=>$program['id']))?>" class="blue" target="_blank">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
								</td>
							</tr>
						<?php endforeach?>
					<?php endif;?>
					</table>
		<div id="pageStr"><?php echo $showPage;?></div>
	</div>
</div>
</div>
</body>
</html>