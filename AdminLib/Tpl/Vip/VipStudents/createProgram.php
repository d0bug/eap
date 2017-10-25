<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>生成辅导方案</title>
	<meta name="keywords" content="" />
	<meta name="description" content=""/>
	<link href="/static/css/vip-program.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min2.js"></script>
	<script type="text/javascript" src="/static/js/popup2.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
</head>
<body>
<form method="POST" action="<?php echo U('Vip/VipStudent/doCreateProgram',array('id'=>$testCoachId))?>">
<div class="fd-top">
	<div class="fd-wauto fd-top-bg"><a href="index.html" class="fd-fl"><img src="/static/images/fd-01.png"></a><p class="fd-fl"><?php echo $studentInfo['sstudentname']?><i> 同学的 </i><?php echo $testCoachInfo['subjectname']?><i> 辅导方案</i><span class="fa-time"><?php echo $date;?></span></p></div>
</div>
<div class="fd-wauto fd-con">
	<p class="fd-con-title">基本信息 </p>
	<div class="fd-content">
		<p class="fd-con-info" ><?php echo $studentInfo['sstudentname']?>同学，当前为<i><?php echo $studentInfo['gradename']?> </i>，其教师为<i><?php echo $userInfo['real_name']?>老师</i>、学管师为<i><?php echo $studentInfo['sclassadvisername']?>老师</i>，所属校区为<i><?php echo $studentInfo['sdeptname']?></i>。</p>
		<input type="hidden" id="student_code" name="student_code" value="<?php echo $studentInfo['sstudentcode']?>">
		<input type="hidden" id="student_name" name="student_name" value="<?php echo $studentInfo['sstudentname']?>">
		<input type="hidden" id="grade_name" name="grade_name" value="<?php echo $studentInfo['gradename']?>">
		<input type="hidden" id="teacher_code" name="teacher_code" value="<?php echo $userInfo['sCode']?>">
		<input type="hidden" id="teacher_name" name="teacher_name" value="<?php echo $userInfo['real_name']?>">
		<input type="hidden" id="classadviser_name" name="classadviser_name" value="<?php echo $studentInfo['sclassadvisername']?>">
		<input type="hidden" id="dept_code" name="dept_code" value="<?php echo $studentInfo['sdeptcode']?>">
		<input type="hidden" id="dept_name" name="dept_name" value="<?php echo $studentInfo['sdeptname']?>">
		<input type="hidden" id="subject_code" name="subject_code" value="<?php echo $testCoachInfo['ssubjectcode']?>">
		<input type="hidden" id="subject_name" name="subject_name" value="<?php echo $testCoachInfo['subjectname']?>">
		<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $studentInfo['skechengcode']?>">
		<input type="hidden" id="kecheng_name" name="kecheng_name" value="<?php echo $studentInfo['skechengname']?>">
		<input type="hidden" id="start" name="start" value="<?php echo $start?>">
		<input type="hidden" id="end" name="end" value="<?php echo $end?>">
	</div>

	<p class="fd-con-title">近期学习情况 </p>
	<div class="fd-content fd-classlist">
		<?php echo $cloudHtml?>
	</div>
	<div class="fd-content fd-f15">
			<p class="fd-mb10">※ 以下知识点还没有完全消化吸收，点滴铸就辉煌，不要放过任何一个知识点。</p>
			<ul class="fd-con-zhishidian clearfix">
			<?php foreach ($accuracyLowKnowledge as $key=>$knowledge):?>
				<li><?php echo $key+1;?>.<?php echo $knowledge?></li>
			<?php endforeach;?>
			</ul>
			<p class="fd-mb10">※ 孩子的错题已经全部导入到错题书包中，记得让孩子及时改正错题，问题不要遗留哦！</p>
	</div>

	<p class="fd-con-title">课堂表现 sdfsdfdsfs</p>
	<div class="fd-content clearfix ">
		<div class="fd-fl fd-con-xing fd-con-teacherxing">
			<ul>
				<?php foreach ($dimensionArr as $key=>$dimension):?>
				<li>
					<span><?php echo $dimension['title']?>：</span>
					<i id="fd-XP<?php echo $key+1;?>" ></i>
					<div id="fd-XP<?php echo $key+1;?>-jiantou" class="fd-hint-jiantou"></div>
					<samp id="fd-XP<?php echo $key+1;?>-hint" class="fd-hint-text"></samp>
					<input type="hidden" name="dimension_id[]" value="<?php echo $dimension['id']?>">
					<input type="hidden" name="dimension_title[]" value="<?php echo $dimension['title']?>">
				</li>
				<?php endforeach;?>
			</ul> 
		</div>
		
	</div>

	<p class="fd-con-title">课程规划  </p>
	<div class="fd-content">
		<div>
			<bottom class="fd-greenbutton" onclick="testMessageBox_add_programLesson(event,'<?php echo U('Vip/VipStudents/addProgramLesson',array('new_key'=>count($programLesson)))?>','<?php echo U('Vip/VipStudents/saveRaty')?>')">添加课程</bottom>
		</div>
		<ul class="fd-con-geihua" id="lesson">
		<?php if($programLesson):?>
			<?php foreach ($programLesson as $key=>$lesson):?>
			<li class="clearfix" id="lesson_<?php echo $key;?>">
				<div class="fd-con-geihua-left">
					<p class="fd-c222" id="no_<?php echo $key;?>">第<?php echo $lesson['lesson_no']?>次课：</p>
					<p>难易成度：</p>
					<p>重  难 点：</p>
				</div>
				<div class="fd-con-geihua-right fd-con-delete">
					<p id="topic_<?php echo $key;?>"><?php echo $lesson['lesson_topic']?></p>
					<p class="fd-con-geihua-rightxing" id="difficulty_<?php echo $key;?>"><?php if($lesson['lesson_difficulty']==1):?>★<?php elseif ($lesson['lesson_difficulty']==2):?>★★<?php else:?>★★★<?php endif;?></p>
					<p class="fd-c888" id="major_<?php echo $key;?>"><?php echo $lesson['lesson_major']?> </p>
				</div>
				<div class="fd-fl">
					<bottom class="fd-con-teacher-edit" onclick="testMessageBox_edit_programLesson(event,'<?php echo U('Vip/VipStudents/editProgramLesson',array('key'=>$key))?>','<?php echo U('Vip/VipStudents/saveRaty')?>')">编辑</bottom>
					<bottom class="fd-con-teacher-delete" onclick="delete_programLesson('<?php echo $key?>','<?php echo U('Vip/VipStudents/deleteProgramLesson')?>','<?php echo U('Vip/VipStudents/saveRaty')?>')">删除</bottom>
				</div>
			</li>
			<?php endforeach;?>
		<?php endif;?>
		</ul>
	</div>
	<img style="margin:0 auto 30px auto;" src="/static/images/fd-08.png">
</div>

<bottom class="fd-button" onclick="return createProgramHtml('<?php echo U('Vip/VipStudents/createProgramHtml',array('testCoachId'=>$testCoachId))?>','<?php echo U('Vip/VipStudents/saveRaty')?>','<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$testCoachInfo['sstudentcode']))?>')">生成辅导方案</bottom>
</form>


<script type="text/javascript">
$.fn.raty.defaults.path="/static/images"
<?php foreach ($dimensionArr as $key=>$dimension):?>
	$('#fd-XP<?php echo $key+1?>').raty({
		<?php if($level[$key]):?>score:<?php echo $level[$key]?>,<?php endif;?>
		hints:[<?php foreach ($dimension['text'] as $k=>$v):?>'<?php echo $v['text']?>',<?php endforeach;?>],
		target: '#fd-XP<?php echo $key+1?>-hint',
		targetKeep: true,
		click:function(){$('#fd-XP<?php echo $key+1?>-hint, #fd-XP<?php echo $key+1?>-jiantou').addClass("fd-block")}
	});
<?php endforeach;?>
</script>

</body>
</html>    




