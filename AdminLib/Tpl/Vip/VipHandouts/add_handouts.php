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
			teacher_version: {
				required: true,
			},
			introduce: {
				maxlength: 2500
			}
		},
		messages: {
			title: {
				required: '请填写讲义标题',
				maxlength: '讲义标题不能超过80字'
			},
			teacher_version: {
				required: '请添加文档'
			},
			introduce: {
				maxlength: '讲义介绍不能多于2500字'
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
	<h2><?php if($hid):?>编辑讲义<?php else:?>教师上传讲义<?php endif;?></h2>
	<form id="add_handouts" name="add_handouts" method="POST" enctype="multipart/form-data" action="<?php echo U('Vip/VipHandouts/add_handouts');?>">
	<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
	<input type="hidden" id="delimg_url" name="delimg_url" value="<?php echo U('Vip/VipInfo/del_img')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"></td>
			<td><input type="hidden" id="type" name="type" value="0"></td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>讲义标题： </td>
			<td>
				<input type="text" id="title" name="title"  placeholder="输入讲义名称..." value="<?php echo $handoutsInfo['title'];?>" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"><span id="titleMsg">还可输入100个字</span>
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top">讲义缩略图： </td>
			<td  valign="top">
			<span id="upload_handouts_picture"></span>
			<span id="view_picture">
			<?php if($handoutsInfo['picture_show']):?><img src="<?php echo $handoutsInfo['picture_show'];?>">&nbsp;&nbsp;<a href="#" onclick="del_img('<?php echo $handoutsInfo['picture'];?>','#view_picture','#picture')">删除</a><?php endif;?>
			</span>
			<input type="hidden" name="picture" id="picture" value="<?php echo $handoutsInfo['picture'];?>">
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top"><font color="red">*</font>讲义文档： </td>
			<td valign="top"><input type="hidden" id="is_preview" name="is_preview" value="1">
				<span id="upload_item_bank"></span><label id="upload_itembank_msg" class="success"></label><br>
				<span id="view_teacher_file">
					<?php if($handoutsInfo['teacher_version']):?><a href="#"><?php echo $handoutsInfo['teacher_version_show'];?></a>&nbsp;&nbsp;<a href="#" onclick="del_img('<?php echo $handoutsInfo['teacher_version'];?>','#view_teacher_file','#teacher_version','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><?php endif;?>
				</span>
				<input type="hidden" id="teacher_version" name="teacher_version" value="<?php echo $handoutsInfo['teacher_version'];?>">
				<input type="hidden" id="teacher_version_preview" name="teacher_version_preview" value="<?php echo $handoutsInfo['teacher_version_preview'];?>">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class="alt">讲义介绍:</td>
			<td>
				<textarea id="introduce" name="introduce" cols="70" rows="5" placeholder="请输入讲义介绍..."><?php echo $handoutsInfo['introduce'];?></textarea>(2500字以内)
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><?php if($hid):?><input type="hidden" id="action" name="action" value="update"><input type="hidden" id="hid" name="hid" value="<?php echo $hid;?>">
			    <?php else:?><input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value=""><?php endif;?>
			<?php if($permInfo['permValue']==3):?><button type="submit" class="btn">确认提交</button><?php endif;?></td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
		<div style="color:red">上传须知：</div>
			1. 每次必须上传1份文档，文档不超过10M；<br>
			2. 为了保证讲义能正常显示，仅支持以下格式的文档上传；<br>
			&nbsp;&nbsp;&nbsp;&nbsp;.pdf格式<!--：<img src="/static/images/pdf.png">-->；.doc格式；.docx格式；<!--:<img src="/static/images/word.png">-->.ppt格式；.xls格式；。<br>
		</div>
	<br><br><br><br>
</div>
</div>
</body>
</html>