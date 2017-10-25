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
			money: {
				required: true,
				number: true
			},
			num: {
				required: true,
				number: true,
				max:1000
			},
			endtime: {
				required: true,
			}
		},
		messages: {
			money: {
				required: '请填写充值金额',
				number: '充值金额必须为数字'
			},
			num: {
				required: '请填写生成数量',
				number: '生成数量必须为数字',
				max:'生成数量不能大于1000'
			},
			endtime: {
				required: '请填写失效日期',
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
<body>
<div region="center">
<div id="main">
	<h2>生成充值卡</h2>
	<form id="add_form" name="add_form" method="POST" action="" >
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt" style="width:80px"><font color=red>*</font>充值金额：</td>
			<td><input type="text" id="money" name="money" value="" placeholder="输入充值金额" size="30" >元</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>生成数量：</td>
			<td><input type="text" id="num" name="num" value="" placeholder="输入生成数量" size="30" "> </td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>失效日期：</td>
			<td><input type="text" id="endtime" name="endtime" value="" placeholder="输入失效日期" size="30" class="Wdate" onClick="WdatePicker({minDate:'<?php echo date('Y-m-d')?>'})">&nbsp;&nbsp;
				<span class="gray">如果未能在指定日期前激活使用，该学习卡将过期失效。</span>
			</td>
		</tr>
		<tr><td>&nbsp;</td>
			<td>
			<input type="submit" name="save_close" class="btn" value="生成充值卡">
			</td></tr>
	</table>
	</form>
</div>
</div>
</body>
</html>