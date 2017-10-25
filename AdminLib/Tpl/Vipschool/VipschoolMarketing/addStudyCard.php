<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#add_form").validate({
		rules: {
			course_name: {
				required: true,
				maxlength: 100
			},
			num: {
				required: true,
				number: true,
				max:1000
			},
			endtime: {
				required: true,
			},
			limit_day: {
				required: true,
				number: true
			},
		},
		messages: {
			course_name: {
				required: '请填写指定课程',
				maxlength: '指定课程名称不能超过100字'
			},
			num: {
				required: '请填写生成数量',
				number: '生成数量必须为数字',
				max:'生成数量不能大于1000'
			},
			endtime: {
				required: '请填写失效日期',
			},
			limit_day: {
				required: '请填写限定时间',
				number: '限定时间必须为数字'
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
	<h2>生成学习卡</h2>
	<form id="add_form" name="add_form" method="POST" action="" >
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt" style="width:80px"><font color=red>*</font>指定课程：</td>
			<td>
				<input type="text" id="course_name" name="course_name" value="" placeholder="输入课程名称" size="100" >
				<span class="gray">请确保课程名称输入正确无误，否则将无法生成学习卡</span>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>生成数量：</td>
			<td><input type="text" id="num" name="num" value="" placeholder="输入生成数量" size="30" "> </td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>失效日期：</td>
			<td><input type="text" id="endtime" name="endtime" value="" placeholder="输入失效日期" size="30" class="Wdate" onClick="WdatePicker({minDate:'<?php echo date('Y-m-d')?>'})">&nbsp;&nbsp;
				<span class="gray">如果未能在指定日期前激活使用，该学习卡将过期失效。</span>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>限定时间：</td>
			<td>
				<input type="text" id="limit_day" name="limit_day" value="" placeholder="输入限定时间" size="10" ">天&nbsp;&nbsp;
				<span class="gray">使用该学习卡购买的课程，将在指定天数后失效。</span>
			</td>
		</tr>
		<tr><td>&nbsp;</td>
			<td>
			<input type="submit" name="save_close" class="btn" value="生成学习卡">
			</td></tr>
	</table>
	</form>
</div>
</div>
</body>
</html>