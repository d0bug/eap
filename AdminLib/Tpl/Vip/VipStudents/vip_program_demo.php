<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" name="课程规划">
<meta name="description" content="课程规划">
	<title>课程规划</title>
	<link rel="stylesheet" type="text/css" href="/static/css/style_2.css">
</head>
<style type="text/css">
.fd-button{width:300px;height:60px;font-size:20px;color:#fff;background:#f90;box-shadow:0 4px 0 #dc8400;display: block;border-radius:6px; text-align: center;line-height:60px;border:0;margin: 0 auto 60px auto;cursor: pointer;border:1px solid #f90}
.fd-button:hover{box-shadow: none;background:#f70;border:1px solid #f70}
</style>
<body>
<!--startprint-->
<div class="banner">
	<div class="banner_con">
		<p class="c32"><span class="red c50"><?php echo $_POST['student_name']?></span>同学<span class="red">专属阶段性</span><span class="blue c50">课程规划</span></p>
		<i class="time c32"><?php echo $today ?></i>
	</div>
</div>
<!--banner结束-->
<div class="class">
	<div class="class_con">
		<div class="class_title">
			<ul>
				<li class="icon">教师:<span class="c24 green blod"><?php echo $_POST['teacher_name'] ?></span></li>
				<li class="icon1">学管师:<span class="c24 green blod"><?php echo $_POST['classadviser_name'] ?></span></li>
				<li class="icon2">年级:<span class="c24 green blod"><?php echo $_POST['grade_name'] ?></span></li>
				<li class="icon3">科目:<span class="c24 green blod"><?php echo $_POST['kecheng_name'] ?></span></li>
			</ul>
		</div>
		<!--title-->
		<?php echo $lesson_html; ?>
		<bottom class="fd-button" onclick="printpr()">打印辅导方案</bottom>
		<!--contain-->
		<div class="class_footer">
		</div>
	</div>
	<div class="luright"></div>
	<div class="luleft"></div>
</div>
<div class="footer"></div>
<!--endprint-->
<!--<bottom class="fd-button" onclick="printpr()">打印辅导方案</bottom>-->
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