<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" >
	<title>辅导方案</title>
	<meta name="keywords" name="辅导方案">
	<meta name="description" content="辅导方案">
	<link rel="stylesheet" type="text/css" href="/static/css/style.css">
</head>
<style type="text/css">
	.fd-button,.fd-greenbutton,.fd-con-teacher-delete,.fd-con-teacher-edit{width:300px;height:60px;font-size:20px;color:#fff;background:#f90;box-shadow:0 4px 0 #dc8400;display: block;border-radius:6px; text-align: center;line-height:60px;border:0;margin: 0 auto 60px auto;cursor: pointer;border:1px solid #f90}
	.fd-button:hover{box-shadow: none;background:#f70;border:1px solid #f70}
</style>
<body>
<!--startprint-->
<div class="banner">
	<div class="banner_con">
		<span class="name"><?php echo $_POST['student_name']?></span>
		<span class="kemu"><?php echo $_POST['subject_name']?></span>
		<span class="time"><?php echo $today?></span>
	</div>
</div>
<div class="conbg"> 
	<div class="contain">
		<div class="b-blue"></div>
		<!--基本信息-->
		<div class="con">
			<div class="title po1">基本信息</div>
			<p class="info"><?php echo $_POST['student_name']?>同学，当前年级为<span><?php echo $_POST['grade_name']?></span>，其老师为<span><?php echo $_POST['teacher_name']?></span>、学管师为<span><?php echo $_POST['classadviser_name']?>老师</span>、所属校区为<span><?php echo $_POST['dept_name']?></span>。</p>
		</div>
		<!--老师说-->
		<div class="con">
			<div class="title po2">老师说</div>
			<p class="info"><?php echo $teacher_say_html; ?></p>
		</div>
		<!--课程规划-->
		<div class="con">
			<div class="title po3">课程规划</div>
			<?php echo $lesson_html; ?>
		</div>
		<bottom class="fd-button" onclick="printpr()">打印课程规划</bottom>
		<div class="b-sblue"></div>
	</div>
</div>

<div class="yun1"></div>
<div class="feiji"></div>
<div class="yun2"></div>
<!--endprint-->
<script type="text/javascript">
//预览函数
function printpr(){
	var bdhtml=window.document.body.innerHTML;//获取当前页的html代码 
	var startStr="<!--startprint-->";//设置打印开始区域 
	var endStr="<!--endprint-->";//设置打印结束区域 
	var printHtml=bdhtml.substring(bdhtml.indexOf(startStr)+startStr.length,bdhtml.indexOf(endStr));//从标记里获取需要打印的页面 
 
	window.document.body.innerHTML=printHtml;//需要打印的页面 
	window.print(); 
	window.document.body.innerHTML=bdhtml;//还原界面 
}
</script>
</body>
</html>
