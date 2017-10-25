<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" >
	<title>辅导方案</title>
	<meta name="keywords" name="辅导方案">
	<meta name="description" content="辅导方案">
	<link rel="stylesheet" type="text/css" href="/static/css/style.css">
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min2.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
</head>
<body>
<form method="POST" action="">
<div class="banner">
	<div class="banner_con">
		<span class="name"><?php echo $studentInfo['sstudentname']?></span>
		<span class="kemu"><?php echo $testCoachInfo['subjectname']?></span>
		<span class="time"><?php echo $date;?></span>
	</div>
</div>
<div class="conbg"> 
	<div class="contain">
		<div class="b-blue"></div>
		<!--基本信息-->
		<div class="con">
			<div class="title po1">基本信息</div>
			<p class="info"><?php echo $studentInfo['sstudentname']?>同学，当前年级为<span><?php echo $studentInfo['gradename']?></span>，其老师为<span><?php echo $userInfo['real_name']?></span>、学管师为<span><?php echo $studentInfo['sclassadvisername']?>老师</span>、所属校区为<span><?php echo $studentInfo['sdeptname']?></span>。</p>
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
		<!--老师说-->
		<div class="con">
			<div class="title po2">老师说</div>
			<textarea placeholder="请输入学生近期学习情况" class="txt" name="comment" id="comment"><?php echo htmlspecialchars_decode($programInfo['comment']);?></textarea>
		</div>
		<!--课程规划-->
		<div class="con">
			<div class="title po3">课程规划</div>
			<?php for($i=1;$i<=10;$i++): ?>
			<!--第1次课-->
			<div class="class lesson_<?php echo $i; ?>">
				<div class="classtitle">
					<h3 class="classnum">第<input type="text"  name="lesson_no[]" class="num" value="">次课</h3>
					<p>难易程度&nbsp;&nbsp;&nbsp;&nbsp;<span class="medal_<?php echo $i?>"></span></p>
					<script type="text/javascript">
					$('.medal_'+<?php echo $i;?>).raty({
						score:1,
						number:3,
						targetKeep: true,
					});
					</script>
				</div>
				<div class="classcon">
					<p style="margin-bottom:10px;"><label>课次主题</label><input type="text" name="lesson_topic[]" value="" placeholder="请输入课次主题" class="tet"></p>
					<p><label>重难点</label><textarea placeholder="请输入重难点" name="lesson_major[]" class="area"></textarea></p>
				</div>
			</div>
			<?php endfor; ?>
		</div>
		<div class="add">+添加课程</div>
		<a href="#" class="report" onclick="return createProgramHtmlNew('<?php echo U('Vip/VipStudents/createProgramHtmlNew',array('testCoachId'=>$testCoachId))?>','<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$testCoachInfo['sstudentcode']))?>')">生成辅导方案</a>
		<div class="b-sblue"></div>
	</div>
</div>
</form>
<div class="yun1"></div>
<div class="feiji"></div>
<div class="yun2"></div>
</body>
</html>
<script type="text/javascript">
$(".add").click(function(){
	//div个数
	var lengths= $(".con>div.class").size();
	var lesson_no=lengths+1;
	var addStr='<div class="class lesson_'+lesson_no+'">'+
	'<div class="classtitle">'+
	'<h3 class="classnum">第<input type="text"  class="num" name="lesson_no[]" value="">次课</h3>'+
	'<p>难易程度&nbsp;&nbsp;&nbsp;&nbsp;<span class="medal_'+lesson_no+'"></span></p><a href="javascript:;" class="del" onclick="return del('+lesson_no+');">删除</a>'+
	'</div>'+
	'<div class="classcon">'+
	'<p style="margin-bottom:10px;"><label>课次主题</label><input type="text" name="lesson_topic[]"  value="" placeholder="请输入课次主题" class="tet"></p>'+
	'<p><label>重难点</label><textarea placeholder="请输入重难点" name="lesson_major[]" class="area"></textarea></p>'+
	'</div></div>';

	addStr+="<script>$('.medal_"+lesson_no+"').raty({"+
			"score:1,"+
			"number:3,"+
			"targetKeep: true,"+
			"});<\/script>";

	$(".lesson_"+lengths).after(addStr);

});

//删除
function del(l)
{
	$(".lesson_"+l).remove();
}
</script>

