<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#addnews_form").validate({
		rules: {
			ntype: {
				required: true
			},
			title: {
				required: true,
				maxlength: 80
			},
		},
		messages: {
			ntype: {
				required: '请选择资讯类型'
			},
			title: {
				required: '请填写资讯标题',
				maxlength: '资讯标题不能超过80个字符'
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
<h2>资讯上传</h2>
	<form id="addnews_form" name="addnews_form" method="POST" action="<?php echo U('Vip/VipNews/add_news')?>"  enctype="multipart/form-data" onsubmit="return check_editor()">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt" style="width:80px"><font color=red>*</font>类型：</td>
			<td>
				<select id="ntype" name="ntype">
					<?php foreach($articleType as $type):?>
						<option value="<?php echo $type;?>"><?php echo $type;?></option>
					<?php endforeach?>
				</select>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>标题：</td>
			<td><input type="text" id="title" name="title" value="" placeholder="输入资讯标题..." size="80" onkeydown="return check_length('title','titleMsg',80)" onkeyup="return check_length('title','titleMsg',80)"> <span id="titleMsg">还可输入80个字</span>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>内容：</td>
			<td><textarea name="ncontent" id="ncontent" style="width:60%;height:400px" placeholder="输入资讯内容..."></textarea><span class="error"></span>
	            <?php echo W('Editor', array('id'=>'ncontent'))?>
	        </td>
	    </tr>
		<tr><td>&nbsp;</td>
			<td>
			<?php if($permInfo['permValue']==3):?><input type="submit" name="save_close" class="btn" value="保存并关闭"></button>&nbsp;&nbsp;<input type="submit" name="save_add" class="btn" value="保存后继续发表"></button><?php endif;?>			
			</td></tr>
	</table>
	</form>
</div>
</div>
</body>
</html>