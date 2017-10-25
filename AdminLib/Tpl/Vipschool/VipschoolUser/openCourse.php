<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#open_course").validate({
		rules: {
			course_id: {
				required: true,
				number: true
			},
			uid: {
				required: true,
				number: true
			},
			endtime: {
				required: true,
			},
		},
		messages: {
			course_id: {
				required: '请填写课程ID',
				number: '课程ID必修为数字'
			},
			uid: {
				required: '请填写用户ID',
				number: '用户ID必修为数字'
			},
			endtime: {
				required: '请填写截止时间',
			},
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
<body>
<div region="center">
<div id="main">
	<h2>课程开通</h2>
	<form id="open_course" method="POST" action="">
		<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt">课程ID：</td>
				<td><input type="text" id="course_id" name="course_id" value="">&nbsp;<font color=orange>数据源：课程管理-》课程列表-》第一列ID值</font></td>
			</tr>
			<tr><td class="alt">选择学员：</td>
				<td><input type="text" id="uid" name="uid" value="">&nbsp;<font color=orange>数据源：账号管理-》用户管理-》第一列ID值</font></td>
			</tr>
			<tr><td class="alt">截止时间：</td>
				<td><input type="text" id="endtime" name="endtime" value="" class="Wdate" onClick="WdatePicker({minDate:'<?php echo date('Y-m-d H:i:s')?>'})">&nbsp;<font color=orange>课程观看权限截止日期</font></td>
			</tr>
			<tr><td class="alt">&nbsp;</td>
				<td><input type="submit"  value="确定开通"></td>
			</tr>
		</table>
	</form>
</div>
</div>
</body>
</html>