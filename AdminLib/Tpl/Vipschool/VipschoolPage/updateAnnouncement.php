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
	$("#addnews_form").validate({
		rules: {
			title: {
				required: true,
				maxlength: 100
			},
			'focus[]': {
				required: true,
				
			},
			keywords: {
				required: true,
				maxlength: 100
			},
			description: {
				required: true,
				maxlength: 200
			},
		},
		messages: {
			title: {
				required: '请填写活动标题',
				maxlength: '活动标题长度不能超过100字'
			},
			'focus[]': {
				required: '请上传活动宣传图片',
			},
			keywords: {
				required: '请填写关键词',
				maxlength: '关键词不能超过100字'
			},
			description: {
				required: '请填写活动描述',
				maxlength: '活动描述不能超过200字'
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
<h2>编辑公告</h2>
	<form id="addnews_form" name="addnews_form" method="POST" action=""  enctype="multipart/form-data" onsubmit="return check_editor()">
	<input type="hidden" id="aid" name="aid" value="<?php echo $announcementInfo['id']?>">
	<input type="hidden" id="autocut" name="autocut" value="1">
	<input type="hidden" id="new_width" name="new_width" value="622">
	<input type="hidden" id="new_height" name="new_height" value="301">
	<input type="hidden" id="num" name="num" value="1">
	<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vipschool/VipschoolPage/uploadFile')?>">
	<input type="hidden" id="delete_url" name="delete_url" value="<?php echo U('Vipschool/VipschoolPage/deleteObject')?>">
	<input type="hidden" id="img_width" name="img_width" value="30%">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt" style="width:80px"><font color=red>*</font>活动标题：</td>
			<td><input type="text" id="title" name="title" value="<?php echo $announcementInfo['title']?>" placeholder="输入活动标题..." size="100" onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"> <span id="titleMsg">还可输入100个字</span></td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>关键词：</td>
			<td><input type="text" id="keywords" name="keywords" value="<?php echo $announcementInfo['keywords']?>" placeholder="输入关键词..." size="100" "> 
				<span>多个关键词之间用英文的逗号（,）分隔，建议80字以内。</span>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>活动描述：</td>
			<td><textarea id="description" name="description" cols="100" rows="2" placeholder="输入活动描述..."><?php echo $announcementInfo['description']?></textarea> <span>描述建议160字以内。</span></td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>活动图片：</td>
			<td>
				<div>
					<span id="upload_focus_1" class="upload"></span>
					<span id="view_focus_1" class="view_file">
					<?php if(!empty($announcementInfo['img'])):?>
						<img src="<?php echo $announcementInfo['show_img']?>" ><a href='#none' onclick="del_file('<?php echo $announcementInfo['img']?>','#view_focus_1','#focus_1','#upload_focus_1_msg','<?php echo U('Vipschool/VipschoolPage/deleteObject')?>')">删除图片</a>
					<?php endif;?>
					</span>
					<input type="hidden" id="focus_1" name="focus[]" value="<?php echo $announcementInfo['img']?>">
					<div id="upload_focus_1_msg"></div>
				</div>
			</td>
		</tr>
		<tr><td class="alt" style="width:80px"><font color=red>*</font>活动内容：</td>
			<td><textarea name="ncontent" id="ncontent" style="width:60%;height:500px" placeholder="输入活动内容..."><?php echo $announcementInfo['content']?></textarea><span id="ncontent_msg" class="error"></span>
	            <?php echo W('EditorOss', array('id'=>'ncontent','layout'=>'simple'))?>	
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