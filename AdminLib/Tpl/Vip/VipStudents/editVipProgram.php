<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" name="课程规划">
<meta name="description" content="课程规划">
	<title>课程规划</title>
	<link rel="stylesheet" type="text/css" href="/static/css/style_2.css">
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
</head>
<body>
<form method="POST" action="">
<div class="banner">
	<div class="banner_con">
		<p class="c32"><span class="red c50"><?php echo $programInfo['student_name'] ?></span>同学<span class="red">专属阶段性</span><span class="blue c50">课程规划</span></p>
		<i class="time c32"><?php echo $programInfo['addtime']; ?></i>
	</div>
</div>
<input type="hidden" id="student_code" name="student_code" value="<?php echo $programInfo['student_code']?>">
<input type="hidden" id="student_name" name="student_name" value="<?php echo $programInfo['student_name']?>">
<input type="hidden" id="grade_name" name="grade_name" value="<?php echo $programInfo['grade_name']?>">
<input type="hidden" id="teacher_code" name="teacher_code" value="<?php echo $programInfo['teacher_code']?>">
<input type="hidden" id="teacher_name" name="teacher_name" value="<?php echo $programInfo['teacher_name']?>">
<input type="hidden" id="classadviser_name" name="classadviser_name" value="<?php echo $programInfo['classadviser_name']?>">
<input type="hidden" id="dept_code" name="dept_code" value="<?php echo $programInfo['dept_code']?>">
<input type="hidden" id="dept_name" name="dept_name" value="<?php echo $programInfo['dept_name']?>">
<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $programInfo['kecheng_code']?>">
<input type="hidden" id="kecheng_name" name="kecheng_name" value="<?php echo $programInfo['kecheng_name']?>">
<!--banner结束-->
<div class="class">
	<div class="class_con">
		<div class="class_title">
			<ul>
				<li class="icon">教师:<span class="c24 green blod"><?php echo $programInfo['teacher_name'] ?></span></li>
				<li class="icon1">学管师:<span class="c24 green blod"><?php echo $programInfo['classadviser_name']; ?></span></li>
				<li class="icon2">年级:<span class="c24 green blod"><?php echo $programInfo['grade_name'] ?></span></li>
				<li class="icon3">科目:<span class="c24 green blod"><?php echo $programInfo['kecheng_name'] ?></span></li>
			</ul>
		</div>
		<?php if(!empty($programInfo['program_lesson'])): ?>
			<?php foreach($programInfo['program_lesson'] as $k => $v): ?>
			<input type="hidden" name="lesson_no[]" value="<?php echo $v['lesson_no']; ?>">
			<div class="class_contain lesson_<?php echo $v['lesson_no']; ?>">
				<div class="class_name">
					<p class="c18 blod">第<span class="green"><?php echo $v['lesson_no']; ?></span>次课</p>
					
				</div>
				<div class="from">
					<input type="text" class="name" value="<?php echo $v['lesson_topic']; ?>" name="lesson_topic[]"  placeholder="请填写课程名称">
					<textarea  placeholder="请输入重难点" class="area" name="lesson_major[]"><?php echo $v['lesson_major']; ?></textarea>
				</div>
			</div>
		<?php endforeach; ?>
		<?php else: ?>
		<?php for($i=1;$i<=10;$i++): ?>
			<input type="hidden" name="lesson_no[]" value="<?php echo $i; ?>">
			<div class="class_contain lesson_<?php echo $i; ?>">
				<div class="class_name">
					<p class="c18 blod">第<span class="green"><?php echo $i ?></span>次课</p>
					
				</div>
				<div class="from">
					<input type="text" class="name" value="" name="lesson_topic[]"  placeholder="请填写课程名称">
					<textarea  placeholder="请输入重难点" class="area" name="lesson_major[]"></textarea>
				</div>
			</div>
		<?php endfor; ?>
		<?php endif; ?>
		<div class="add">+添加课程</div>
		<a href="#" class="report c20" onclick="return addProgram('<?php echo U('Vip/VipStudents/editVipProgram',array('id'=>$programInfo['id']))?>','<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$programInfo['student_code']))?>')">保存</a>
		<!--contain-->
		<div class="class_footer">
		</div>
	</div>
	<div class="luright"></div>
	<div class="luleft"></div>
</div>
</form>
<div class="footer"></div>
<script type="text/javascript">
$(".add").click(function(){
	//div个数
	var lengths= $(".class_con>div.class_contain").size();
	var lesson_no=lengths+1;
	var addStr='<div class="class_contain lesson_'+lesson_no+'">'+
			'<div class="class_name">'+
				'<input type="hidden" name="lesson_no[]" value="'+lesson_no+'"><p class="c18 blod">第<span class="green">'+lesson_no+'</span>次课</p>'+
				'<a href="javascript:;" class="del" onclick="return del('+lesson_no+');">删除</a>'+
			'</div>'+
			'<div class="from">'+
				'<input type="text" class="name" value="" placeholder="请填写课程名称" name="lesson_topic[]">'+
				'<textarea  placeholder="请输入重难点" name="lesson_major[]" class="area"></textarea>'+
			'</div>'+
			'</div>';
	$(".lesson_"+lengths).after(addStr);
});

//删除
function del(l)
{
	$(".lesson_"+l).remove();
}
</script>
</body>
</html>