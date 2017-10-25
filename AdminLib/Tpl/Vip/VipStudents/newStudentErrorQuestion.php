<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<link href="/static/css/paper.css" type="text/css" rel="stylesheet" />
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
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
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentMessage',array('student_code'=>$student_code))?>">留言板</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentInfo',array('student_code'=>$student_code))?>">学员详情</a>
			</li>
			<li class="current">
				<a href="<?php echo U('Vip/VipStudents/newStudentErrorQuestion',array('student_code'=>$student_code))?>">错题书包</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$student_code))?>">课程规划</a>
			</li>
		</ul>
	</div><br/>
	
	<div id="search">
		<form id="form" method="GET" >
			上课日期:<input type="text"  class="Wdate" id="start" name="start" value="<?php echo $start;?>" placeholder="开始日期" onClick="WdatePicker()">
			至&nbsp;<input type="text"  class="Wdate" id="end" name="end" value="<?php echo $end;?>" placeholder="截止日期" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(start)}'});"> &nbsp;
			课次主题：<input type="text" placeholder="请输入课次主题" id="lesson_topic" name="lesson_topic" value="<?php echo $lesson_topic;?>" size="40"> &nbsp;
			错题类型：<select id="type" name="type">
						<option value="" <?php if(empty($type)):?>selected<?php endif;?>>全部</option>
						<option value="1" <?php if($type==1):?>selected<?php endif;?>>例题</option>
						<option value="2" <?php if($type==2):?>selected<?php endif;?>>随堂练习</option>
						<option value="3" <?php if($type==3):?>selected<?php endif;?>>作业</option>
					</select>&nbsp;&nbsp;
			<input type="submit" class="btn" value="搜索">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="#" class="blue" onclick="print_preview(1)">打印</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo U('Vip/VipStudents/exportErrorQuestion',array('student_code'=>$student_code,'student_name'=>$studentInfo['sstudentname'],'start'=>$start,'end'=>$end,'lesson_topic'=>$lesson_topic));?>" class="blue" >导出</a>
		</form>
	</div>
	<!--startprint1-->
	<div id="list" class="clearfix">
		<?php if(!empty($errorQuestionList)):?>
			<?php foreach($errorQuestionList as $key=>$errorQuestion):?>
			<div id="row" class="question-box">
				<div class="title">
					<span>上课日期：<?php echo $errorQuestion['dtdatereal']?></span>
					<span style="margin-left:40px">课次主题：<?php echo $errorQuestion['lesson_topic']?></span>
					<span style="margin-left:40px">课程名称：<?php echo $errorQuestion['skechengname']?>（<?php echo $errorQuestion['skechengcode']?>）</span>
					<span style="margin-left:40px">错题类型：<?php if($errorQuestion['type']==1):?>例题<?php elseif ($errorQuestion['type']==2):?>随堂练习<?php elseif ($errorQuestion['type']==3):?>作业<?php endif;?></span>
					<span style="float:right;margin-right:100px"><a href="#" class="blue" onclick="delete_error_question('<?php echo $errorQuestion['id']?>','<?php echo U('Vip/VipStudents/deleteErrorQuestion')?>')">删除记录</a></span>
				</div>
				<div id="question_<?php echo $key?>" onclick="javascript:$('#analysis_<?php echo $key?>').toggle()" style="cursor:pointer" class="bd clearfix">
					<div class="con">
						<table>
							<tbody>
								<tr>
									<td valign="top" >
										<dl class="opt">
											<dt style="width:auto"><?php echo ($curPage-1)*$pagesize+$key+1?></dt>
											<dd>、</dd>
										</dl>
									</td>
									<td style="clear:both"><?php echo $errorQuestion['question_desc']['content']?></td>
								</tr>
							</tbody>
						</table>
						
					<?php if(!empty($errorQuestion['question_option'])):?>
						<table>
							<?php foreach ($errorQuestion['question_option'] as $k=>$option):?>
							<tbody>
								<tr>
									<td>
										<dl class="opt">
											<dt><?php echo $optionKeyArr[$k]?></dt>
											<dd>．</dd>
										</dl>
									</td>
									<td style="margin: 0 auto;color:#333;font-family:'微软雅黑';font-size:14px;"><?php echo $option['content']?></td>
								</tr>
							</tbody>
							<?php endforeach?>
						</table>
					
				<?php endif;?>

					<div id="analysis_<?php echo $key?>" class="answer pointer" onclick="$(this).hide()" >
						<div class="box">
							<table>
								<tbody>
									<tr><td valign="middle" style="height: 28px;">【答案】</td></tr>
									<tr><td><?php echo $errorQuestion['question_desc']['answer_content']?></td></tr>
									<tr><td valign="middle" style="height: 28px;">【解析】</td></tr>
									<tr><td><?php echo $errorQuestion['question_desc']['analysis']?></td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach?>
		<?php endif;?>
	</div>
	<!--endprint1--> 
	<div id="pageStr" style="margin-left:30px"><?php echo $showPage;?></div>
</div>
</div>

<script>
$('span[lang=EN-US]').css('top', '');
$('span[lang=EN-US]').find('span').css('top', '');
</script>
</body>
</html>