<!doctype html>
<html>
<head>
    <title>FlexPaper</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />
    <link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min2.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
</head>
<body>
<div region="center">
<table  class="tableInfo" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="alt">课次：</td><td>第<input type="text" id="lesson_no" name="lesson_no" value="" size="5">课</td>
	</tr>
	<tr>
		<td class="alt">难度：</td><td>
			<select id="lesson_difficulty" name="lesson_difficulty">
				<option value="">请选择难度</option>
				<option value="1">一星（容易）</option>
				<option value="2">二星（中等）</option>
				<option value="3">三星（困难）</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="alt">课次主题：</td><td><input type="text" id="lesson_topic" name="lesson_topic" value="" size="80"></td>
	</tr>
	<tr>
		<td class="alt">重难点：</td><td><textarea id="lesson_major" name="lesson_major" rows="4" cols="80"></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td><td><input type="button" value="确认添加" onclick="return doAddProgramLesson('<?php echo $new_key?>','<?php echo U('Vip/VipStudents/doAddProgramLesson')?>')"><label id="form_msg"></label></td>
	</tr>
</table>
</div>
</body>
</html>