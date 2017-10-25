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
			type: {
				required: true
			},
			'subject': {
				required: true
			},
			'grade': {
				required: true
			},
			'knowledge': {
				required: true,
			},
			'teacher_version[]':{
				required: true,
			},
			'student_version[]':{
				required: true,
			},
			introduce: {
				required: true,
				maxlength: 2500
			}
		},
		messages: {
			type: {
				required: '请选择上传类型'
			},
			'subject': {
				required: '请选择讲义所属科目',
			},
			'grade': {
				required: '请选择课程属性',
			},
			'knowledge': {
				required: '请选择讲义属性',
			},
			'teacher_version[]':{
				required: '请上传教师版讲义',
			},
			'student_version[]':{
				required: '请上传学生版讲义',
			},
			introduce: {
				required: '请填写讲义介绍',
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
	<h2><?php if($hid):?>编辑讲义<?php else:?>教研上传讲义<?php endif;?></h2>
	<form id="add_handouts" name="add_handouts" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipJiaoyan/add_handouts',array('auto_close'=>$auto_close));?>">
	<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
	<input type="hidden" id="delimg_url" name="delimg_url" value="<?php echo U('Vip/VipInfo/del_img')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>上传类型： </td>
			<td>
				课程讲义<input type="hidden" id="type" name="type" value="0">
			</td>
		</tr>
		<?php if($hid):?>
		<tr>
			<td class="alt"><font color="red">*</font>讲义标题： </td>
			<td>
				<input type="text" id="title" name="title" placeholder="请输入讲义标题..." value="<?php echo $handoutsInfo['title'];?>" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"><label id="title_msg" class="error"></label><span id="titleMsg">还可输入100个字</span>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td class="alt" valign="top">讲义缩略图： </td>
			<td  valign="top">
				<span id="upload_handouts_picture"></span>
				<span id="view_picture">
					<?php if($handoutsInfo['picture_show']):?><img src="<?php echo $handoutsInfo['picture_show'];?>">&nbsp;&nbsp;<a href="#" onclick="del_img('<?php echo $handoutsInfo['picture'];?>','#view_picture','#picture','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><?php endif;?>
				</span>
				<input type="hidden" name="picture" id="picture" value="<?php echo $handoutsInfo['picture'];?>">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>所属科目： </td>
			<td>
				<select name="subject" id="subject" onchange="get_grades_option(this.value,'<?php echo U('Vip/VipHandouts/get_grades_option')?>');">
					<option value="">请选择科目</option>
				<?php foreach($subjectArr as $key=>$subject):?>
					<option value="<?php echo $subject['sid'];?>" <?php if($handoutsInfo['sid'] == $subject['sid']):?>selected<?php endif;?>><?php echo $subject['name'];?></option>
				<?php endforeach?>
				</select>
				<label id="subject_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>所属课程属性： </td>
			<td>
				<select name="grade" id="grade" onchange="get_knowledge_option(this.value,'<?php echo U('Vip/VipHandouts/get_knowledge_option')?>');">
					<option value="">请选择课程属性</option>
				<?php foreach($gradeArr as $key=>$grade):?>
					<option value="<?php echo $grade['gid'];?>" <?php if($handoutsInfo['gid'] == $grade['gid']):?>selected<?php endif;?>><?php echo $grade['name'];?></option>
				<?php endforeach?>
				</select>
				<label id="grade_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>所属讲义属性： </td>
			<td>
				<select name="knowledge" id="knowledge" onchange="get_nianji_option(this.value,'#nianjiHtml','<?php echo U('Vip/VipHandouts/get_nianji_option')?>');init_uploadify('#upload','handouts')">
					<?php if($handoutsInfo['kid']):?><option value="<?php echo $handoutsInfo['kid'];?>"><?php echo $handoutsInfo['knowledge_name'];?></option><?php endif;?>
					<option value="">请选择讲义属性</option>
				</select>
				<label id="knowledge_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"> 所属年级： </td>
			<td>
				<div id="nianjiHtml">
				<?php if($handoutsInfo['nids']):?>
					<?php foreach($nianjiArr as $key=>$nianji):?>
						<input type="checkbox" id="nianji_<?php echo $key;?>" name="nianji[]" value="<?php echo $key;?>" <?php if(strpos($handoutsInfo['nids'],','.$key.',')!==false):?>checked<?php endif;?>><?php echo $nianji;?></option>
					<?php endforeach?>
				<?php endif;?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>是否兼职教师可见： </td>
			<td>
				<input type="radio" id="is_parttime_visible_1" name="is_parttime_visible" value="1" <?php if($handoutsInfo['is_parttime_visible'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_parttime_visible_0" name="is_parttime_visible" value="0" <?php if($handoutsInfo['is_parttime_visible'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top"><font color="red">*</font>讲义文档： </td>
			<td valign="top">
				<span style="color:#F16E2B;">注：教师版文档、学生版文档均为必填项</span>&nbsp;&nbsp;
				<span id="add_button"><?php if(!$hid):?><input type="button" value="添加文档" onclick="add_document('#upload')"><?php endif;?></span><label id="document_msg" class="error"></label>
				<div id="upload">
				<?php if($hid):?>
					<li>
						<span id="upload_teacher_version_0"></span><label id="teacher_version_msg_0" class="success"></label><br>
						<span id="view_teacher_file_0" class="view_file">
							<?php if($handoutsInfo['teacher_version']):?><a href="#none"><?php echo $handoutsInfo['teacher_version_show'];?></a>&nbsp;&nbsp;<a href="#none" onclick="del_img('<?php echo $handoutsInfo['teacher_version'];?>','#view_teacher_file_0','#teacher_version_0','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><?php endif;?>
						</span>
						<input type="hidden" id="teacher_version_0" name="teacher_version[]" value="<?php echo $handoutsInfo['teacher_version'];?>">
						<input type="hidden" id="teacher_version_preview_0" name="teacher_version_preview[]" value="<?php echo $handoutsInfo['teacher_version_preview'];?>">
						<span id="upload_student_version_0"></span><label id="student_version_msg_0" class="success"></label><br>
						<span id="view_student_file_0" class="view_file">
							<?php if($handoutsInfo['student_version']):?><a href="#none"><?php echo $handoutsInfo['student_version_show'];?></a>&nbsp;&nbsp;<a href="#none" onclick="del_img('<?php echo $handoutsInfo['student_version'];?>','#view_student_file_0','#student_version_0','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><?php endif;?>
						</span>
						<input type="hidden" id="student_version_0" name="student_version[]" value="<?php echo $handoutsInfo['student_version'];?>">
						<input type="hidden" id="student_version_preview_0" name="student_version_preview[]" value="<?php echo $handoutsInfo['student_version_preview'];?>">
						<input type="hidden" id="student_version_realname_0" name="student_version_realname[]" value="<?php echo $handoutsInfo['title'];?>">
						<label id="upload_handouts_msg_0"></label>
						<div class="t_right">&nbsp;</div>
					</li>
				<?php endif;?>
				</div>
				<input type="hidden" id="document_num" name="document_num" value="<?php if($hid):?>1<?php else:?>0<?php endif;?>"><input type="hidden" id="document_num_real" name="document_num_real" value="<?php if($hid):?>1<?php else:?>0<?php endif;?>"><br>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>讲义介绍:</td>
			<td>
				<textarea id="introduce" name="introduce" cols="70" rows="5" placeholder="请输入讲义介绍..."><?php echo $handoutsInfo['introduce'];?></textarea>(2500字以内)
				<label id="introduce_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><?php if($hid):?><input type="hidden" id="action" name="action" value="update"><input type="hidden" id="hid" name="hid" value="<?php echo $hid;?>">
			    <?php else:?><input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value=""><?php endif;?>
			    <?php if($permInfo['permValue']==3):?><input type="submit" class="btn" onclick="return check_add_handouts()" value="确认提交"><?php endif;?></td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
	<div style="color:red">上传须知：</div>
		1. 每次必须同时上传2份文档（教师版和学生版），每份文档不超过10M；<br>
		2. 为了保证讲义能正常显示，仅支持以下格式的文档上传；<br>
			教师版为PDF格式：<img src="/static/images/pdf.png">；学生版为word格式:<img src="/static/images/word.png">。<br>
	</div>
	<br><br><br><br>
</div>
</div>
</body>
</html>