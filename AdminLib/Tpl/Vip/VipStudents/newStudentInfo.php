<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/popup.js"></script>
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
		</table>
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
				<li class="current">
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
		<div >
			<p class="f_20" style="margin-left:60px;">(<span class="error"><?php echo $studentInfo['sdeptname'];?></span>)学员情况登记</p>
			<div id="content" class="center">
			<table border="1" cellpadding="0" cellspacing="0" width="90%" class="tableInfo">
				<tr>
					<td width="15%">签约日期：</td><td width="18%"><?php echo $studentInfo['dtdate']?></td>
					<td width="15%">咨询师：</td><td width="19%"><?php echo $studentInfo['soperatorname'];?></td>
					<td width="15%">电话：</td><td width="18%"></td>
				</tr>
				<tr>
					<td >学生基本信息：</td>
					<td colspan="5" align="left">
						<p>姓名：<?php echo $studentInfo['sstudentname'];?></p>
						<p>性别：<?php echo ($studentInfo['ngender']==1)?'男':'女';?></p>
						<p>年级：<?php echo $studentInfo['gradename'];?></p>
						<p>所在学校：<?php echo $studentInfo['sschool'];?></p>
						<p>出生日期：<?php echo $studentInfo['dtbirthday'];?></p>
						<p>兴趣爱好：<?php echo $studentInfo['shobby'];?></p>
						<p>学员性格：<?php echo $studentInfo['scharacter'];?></p>
						<p>学员班级排名：<?php echo $studentInfo['nrank'];?></p>
						<p>学员空闲时间：<?php echo $studentInfo['sfeetime'];?></p>
						<p>家长姓氏/电话1：<?php echo $studentInfo['sparents1name']?>
							<?php if($studentInfo['nparents1relation']):?>(<?php echo $studentInfo['nparents1relation']?>)<?php endif;?>
							<?php echo $studentInfo['sparents1phone'];?></p>
						<p>家长姓氏/电话2：<?php echo $studentInfo['sparents2name']?>
							<?php if($studentInfo['nparents2relation']):?>(<?php echo $studentInfo['nparents2relation']?>)<?php endif;?>
							<?php echo $studentInfo['sparents2phone'];?></p>
						<p>家长邮箱：<?php echo $studentInfo['semail'];?></p>
					</td>
				</tr>
				<tr>
					<td >课程信息：</td>
					<td colspan="5" align="left">
						<p>辅导科目：<?php echo $studentInfo['skechengname'];?></p>
						<p>辅导方式：一对一</p>
						<p>班主任：<?php echo $studentInfo['sclassadvisername'];?></p>
						<p>入学成绩测试：<?php echo $studentInfo['stestscores'];?></p>
						<p>教材版本：<?php echo $studentInfo['stextbookversion'];?></p>
						<p>目前学习情况：<?php echo $studentInfo['scurrentlylearning'];?></p>
						<p>首次课日期：<?php echo $studentInfo['dtfristlessondate'];?></p>
						<p>首次课首选时间段：<?php echo $studentInfo['sfristlessontime1'];?></p>
						<p>首次课次选时间段：<?php echo $studentInfo['sfristlessontime2'];?></p>
						<p>首次课内容建议：<?php echo $studentInfo['sfristlessonsuggested'];?></p>
					</td>
				</tr>
				<tr>
					<td >家长期望/建议/要求：</td>
					<td colspan="5" align="left">
						<p>家长建议辅导计划：<?php echo $studentInfo['sparentssuggested'];?></p>
						<p>家长期望目标：<?php echo $studentInfo['sparentsexpect'];?></p>
						<p>对老师要求：<?php echo $studentInfo['sparentsrequest'];?></p>
					</td>
				</tr>
			</table>
			</div>
		
		</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>