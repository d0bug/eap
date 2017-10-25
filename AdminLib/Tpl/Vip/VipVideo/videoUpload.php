<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center" >
	<div id="main">
		<h2><?php if($hid):?>编辑视频信息<?php else:?>视频上传<?php endif;?></h2>
		<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipVideo/videoUpload',array('auto_close'=>$auto_close));?>">
		<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vip/VipVideo/upload_file')?>">
		<!-- <input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipVideo/del_file')?>"> -->
		<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipVideo/del_object')?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt" valign="top"><font color="red">*</font>选择上传视频： </td>
				<td valign="top">
					<span id="upload_video"></span><label id="upload_video_msg" class="success"></label><br>
					<span id="view_video" class="view_file">
					</span>
					<input type="hidden" id="video_url" name="video_url" value="">
					<div class="t_right">&nbsp;</div>
				</td>
			</tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频名称： </td>
				<td>
					<input type="text" id="title" name="title" placeholder="请输入视频名称..." value="" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"><span id="titleMsg">还可输入100个字</span>
				</td>
			</tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频属性： </td>
				<td>
					<select name="attribute_one" id="attribute_one" onchange="get_attributeTwoList(this.value,'<?php echo U('Vip/VipVideo/getAttributeList')?>')">
						<option value="">请选择视频属性</option>
					<?php if(!empty($attributeOneList)):?>
						<?php foreach ($attributeOneList as $key=>$attribute):?>
						<option value="<?php echo $attribute['aid']?>"><?php echo $attribute['name']?></option>
						<?php endforeach;?>
					<?php endif;?>
					</select>
					<label id="attribute_one_msg" class="error"></label>
				</td>
			</tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频类别： </td>
				<td>
					<select name="attribute_two" id="attribute_two" onchange="">
						<option value="">请选择视频类别</option>
					</select>
					<label id="attribute_two_msg" class="error"></label>
				</td>
			</tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频时长： </td>
				<td>
					<input type="text" id="duration" name="duration" placeholder="请输入视频时长..." value="" size="10"  >
				</td>
			</tr>
			<tr>
				<td class="alt" valign="top">视频缩略图： </td>
				<td  valign="top">
					<span id="upload_video_img"></span>
					<span id="view_video_img">
					</span>
					<input type="hidden" name="video_img" id="video_img" value="">
				</td>
			</tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频介绍:</td>
				<td>
					<textarea id="introduce" name="introduce" cols="70" rows="5" placeholder="请输入视频介绍..."></textarea>(2500字以内)
					<label id="introduce_msg" class="error"></label>
				</td>
			</tr>
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
				    <input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value="">
				    <input type="submit" class="btn" value="确认提交">
				</td>
			</tr>
	</div>
</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#add_video").validate({
		rules: {
			video_url: {
				required: true
			},
			title: {
				required: true
			},
			attribute_one: {
				required: true
			},
			attribute_two: {
				required: true
			},
			duration:{
				required: true
			},
			introduce: {
				required: true,
				maxlength: 2500
			}
		},
		messages: {
			video_url: {
				required: '请上传视频'
			},
			title: {
				required: '请填写视频名称'
			},
			attribute_one: {
				required: '请选择视频属性'
			},
			attribute_two: {
				required: '请选择视频类别'
			},
			duration:{
				required: '请填写视频时长'
			},
			introduce: {
				required: '请填写视频介绍',
				maxlength: '视频介绍不超过2500字'
			}
		},

		errorPlacement: function(error, element) {
			if (element.is(':radio') || element.is(':checkbox')) {
				var eid = element.attr('name');
				error.appendTo(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});
	
})

</script>
</html>