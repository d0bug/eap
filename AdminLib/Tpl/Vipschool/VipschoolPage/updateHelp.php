<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#addhelp_form").validate({
		rules: {
			title: {
				required: true,
				maxlength: 50
			},
			
		},
		messages: {
			title: {
				required: '请填写标题',
				maxlength: '标题长度不能超过50字'
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
<h2>编辑帮助</h2>
	<form id="addhelp_form" name="addhelp_form" method="POST" action=""  enctype="multipart/form-data" >
	<input type="hidden" id="hid" name="hid" value="<?php echo $helpInfo['hid']?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt" style="width:80px"><font color=red>*</font>标题：</td>
			<td><input type="text" id="title" name="title" value="<?php echo $helpInfo['title']?>" placeholder="输入标题..." size="100" ></td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>内容：</td>
			<td><textarea name="content" id="content" style="width:60%;height:500px" placeholder="输入内容..."><?php echo $helpInfo['content']?></textarea>
	            <?php echo W('EditorOss', array('id'=>'content','layout'=>'simple'))?>
	        </td>
	    </tr>
		<tr><td>&nbsp;</td>
			<td>
			<input type="submit" name="save_close" class="btn" value="确认修改">
			</td></tr>
	</table>
	</form>
</div>
</div>
</body>
</html>