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
			<li class="current">
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
			<li >
				<a href="<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$student_code))?>">课程规划</a>
			</li>
		</ul>
	</div>
	</div><br/>
		
		<?php if(!empty($testCoachList)):?>
			<?php foreach($testCoachList as $key=>$testCoach):?>
			<div class="programForm">
				<form method="POST" action="<?php echo U('Vip/VipStudents/createProgramNew',array('id'=>$testCoach['id']))?>">
					<div class="h40">课程名称：<?php echo $testCoach['subjectname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;生成截止日期：<?php echo $testCoach['dtauditenddate'];?></div>
<!-- 					<div class="h40">
						上课日期：<input type="text" id="start" name="start" value="" class="Wdate" placeholder="开始日期" onClick="WdatePicker()"> 
							  至 <input type="text" id="end" name="end" value="" class="Wdate" placeholder="截止日期" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(start)}'});"></div> -->
					<div class="h40">
							<input type="submit" value="生成辅导方案" >
							&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="　无效　" onclick="testMessageBox_program_useless(event,'<?php echo U('Vip/VipStudents/programUseLess',array('id'=>$testCoach['id']))?>')">
					</div>
				</form>
			</div>
			<?php endforeach?>
		<?php endif;?>	

	<br>
	<div id="programList">
		<table border="1" width="80%">
			<tr>
				<th align="center">生成时间</th>
				<th align="center">修改时间</th>
				<th align="center">课程名称</th>
				<th align="center">辅导方案</th>
				<th align="center">操作</th>
			</tr>
					<?php if(!empty($programList)):?>
						<?php foreach($programList as $key=>$program):?>
							<tr id="p_<?php echo $program['instime'];?>">
								<td ><?php echo $program['instime'];?></td>
								<td ><?php echo $program['updatetime'];?></td>
								<td ><?php echo $program['subject_name'];?></td>
								<td >
								<?php if(!empty($program['program_arr'])):?>
									<?php foreach ($program['program_arr'] as $k=>$val):?>
										<p>
											<?php echo $program['student_name']?> <?php echo $program['subject_name']?> 辅导方案(<?php echo $k+1?>)&nbsp;&nbsp;
											<?php if($val['is_download']==1):?>
											   	<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>3,'order'=>$k))?>" class="blue">下载</a>
											<?php endif;?>&nbsp;&nbsp;
										</p>
									<?php endforeach;?>
								<?php elseif($program['program_img_is_exist']==1):?>
									<?php echo $program['student_name']?> <?php echo $program['subject_name']?> 辅导方案&nbsp;&nbsp;
									<a href="<?php echo U('Vip/VipStudents/downloadProgramImg',array('id'=>$program['id']))?>" class="blue">下载</a>
								<?php endif;?>
								</td>
								<td align="center">
									<?php if(!empty($program['testcoachid'])):?>
										<?php if($program['program_html_is_exist']==1):?><a href="<?php echo U('Vip/VipStudents/showProgram',array('id'=>$program['id']))?>" class="blue" target="_blank">查看</a><?php endif;?>&nbsp;&nbsp;&nbsp;&nbsp;
										
										<a href="<?php echo U('Vip/VipStudents/editProgramNew',array('id'=>$program['id']))?>" class="blue" target="_blank">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<?php endif;?>
									
								</td>
							</tr>
						<?php endforeach?>
					<?php endif;?>
					</table>
	</div>
</div>
</div>
</body>
</html>