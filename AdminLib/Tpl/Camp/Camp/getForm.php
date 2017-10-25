<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#add_handouts").validate({
		rules: {

			title: {
				required: true,
				maxlength: 80
			},
			subject: {
				required: true
			},
			grade: {
				required: true
			},
			knowledge: {
				required: true,
			},
			teacher_version: {
				required: true,
			},
			student_version: {
				required: true,
			},
			introduce: {
				required: true,
				maxlength: 2500
			}
		},
		messages: {

			title: {
				required: '请填写知识点标题',
				maxlength: '知识点标题不能超过80字'
			},
			subject: {
				required: '请选择知识点所属科目',
			},
			grade: {
				required: '请选择课程属性',
			},
			knowledge: {
				required: '请选择知识点属性',
			},
			teacher_version: {
				required: '请添加教师版知识点'
			},
			student_version: {
				required: '请添加学生版知识点'
			},
			introduce: {
				required: '请填写知识点介绍',
				maxlength: '知识点介绍不能多于2500字'
			}
		},

		errorPlacement: function(error, element) {
			if (element.is(':radio') || element.is(':checkbox')) {
				var eid = element.attr('name');
				error.appendTo(element.parent());
			} else {
				error.insertAfter(element);
			}
		},
	});
})

</script>
</head>
<body >
<div region="center" >
<div id="main">
	<h2>添加班级</h2>
	<form id="form"  method="POST" enctype="multipart/form-data" action="/Camp/Camp/insert">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">



		<tr>
			<td class="alt"><font color="red">*</font>班级编码： </td>
			<td>

				<input type="text" name="sClassCode" id="sClassCode" value="">
			</td>
		</tr>


		<tr>



			<td class="alt"><font color="red">*</font>启用： </td>
			<td>
				<input type="radio" id="status1" name="status" value="1" checked>是&nbsp;&nbsp;
				<input type="radio" id="status0" name="status" value="0" >否
			</td>
		</tr>

		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			   <button type="submit" class="btn">确认提交</button></td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
	<div style="color:red">注意事项：</div>
		1. 注意事项<br>
		2. 注意事项；<br>

	</div>
	<br><br><br><br>
</div>
</div>
</body>
</html>
