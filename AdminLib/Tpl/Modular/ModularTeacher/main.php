<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>教师信息模块化</h2>
	<div class="Snav center">
		<li class="hover" ref="model1" id="step1">1. 上传教师列表资料</li>
		<li ref="model2" id="step2">2. 获取最终表单和代码</li>
	</div>
	<div class="clearit"></div>	
	<div id="main_container" class="center model1 model">
		<form name="form" id="form" method="POST" action="<?php echo U('Modular/ModularTeacher/uploadTeacher')?>" enctype="multipart/form-data" onsubmit="return checkUploadTeacher()"><br>
			<p><input type="file" name="teacherlist" id="teacherlist" value=""><span class="error" id="file_msg"></span>&nbsp;&nbsp;</p>
			<br><br>
			<p>代码结构：<font color=red>(替换规则如下：教师编码：#code、教师姓名：#name、简介：#desc、年级：#grade、学科：#subject、上课地点：#address、图片地址：#thumb、连接地址：#url)</font></p>
			<p><textarea id="html_source" name="html_source" cols="100" rows="20">
<li>
	<div class="modtName"><a href="#url" target="_blank">#name</a></div>
	<div class="modtPhoto"><img src="#thumb" alt="#name"></div>
	<div class="modtInfo">#desc</div>
	<div class="modtCourse">#subject</div>
	<div class="modtGrade">#grade</div>
	<div class="modtAddr">#address</div>
</li>

</textarea></p>
			<p><input type="submit" value=" 上传 " class="btn">&nbsp;（提示：只能上传后缀为xls格式的excel文件，编码为GBK）&nbsp;&nbsp;&nbsp;&nbsp;<a href="/static/images/demo.xls"><font color=red>范例下载</font></a></p><br>
		</form>
	</div>
	</div>
</div>
</body>
</html>