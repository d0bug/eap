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
		<h2>教师列表&nbsp;&nbsp;（服务于<?php echo $moduleCount;?>项目）&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Modular/ModularTeacher/uploadTeacher')?>" class="btn">添加新项目</a></h2>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="teacher_table">
			<tr>
				<th>序号</th>
				<th>模块标题</th>
				<th>需求频道</th>
				<th>使用人次</th>
				<th>添加时间</th>
				<th>统计报表</th>
			</tr>
			<?php foreach($moduleList as $key=>$module):?>
			<tr>
				<td>&nbsp;</td>
				
			</tr>
			<?php endforeach?>
		</table>
		<p><input type="button" class="btn" onclick="toggle('#teacher_table','#flex_btn_teacher')" id="flex_btn_teacher" value="收起"></p><br>
	</div>
</div>
</body>
</html>