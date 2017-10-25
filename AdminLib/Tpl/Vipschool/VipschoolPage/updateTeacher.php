<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#addteacher_form").validate({
		rules: {
			realname: {
				required: true,
				maxlength: 10
			},
			'focus[]': {
				required: true,
			},
			grade: {
				required: true,
			},
			subject: {
				required: true,
			},
			send_word: {
				required: true,
				maxlength: 100
			},
			of_educate_age: {
				required: true,
				number: true
			},
			intro_content: {
				required: true,
				maxlength: 500
			},
			teaching_style: {
				required: true,
				maxlength: 500
			},
			experience_content: {
				required: true,
				maxlength: 500
			},
			comment: {
				required: true,
				maxlength: 500
			},
			'is_onjob': {
				required: true,
			},
		},
		messages: {
			realname: {
				required: '请填写教师姓名',
				maxlength: '教师姓名长度不能超过10个字'
			},
			'focus[]': {
				required: '请上传教师图片',
			},
			grade: {
				required: '请选择年部',
			},
			subject: {
				required: '请选择学科',
			},
			send_word: {
				required: '请填写教师寄语',
				maxlength: '教师寄语长度不能超过500字'
			},
			of_educate_age: {
				required: '请填写教龄',
				number: '教龄必须为数字'
			},
			intro_content: {
				required: '请填写教师简介',
				maxlength: '教师简介长度不能超过500字'
			},
			teaching_style: {
				required: '请填写教学风格',
				maxlength: '教学风格长度不能超过500字'
			},
			experience_content: {
				required: '请填写教学心得',
				maxlength: '教学心得长度不能超过500字'
			},
			comment: {
				required: '请填写家长评价',
				maxlength: '家长评价长度不能超过500字'
			},
			'is_onjob': {
				required: '请选择教师是否在职',
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
<h2>添加教师</h2>
	<form id="addteacher_form" name="addteacher_form" method="POST" action=""  enctype="multipart/form-data" >
	<input type="hidden" id="tid" name="tid" value="<?php echo $teacherInfo['tid']?>">
	<input type="hidden" id="autocut" name="autocut" value="1">
	<input type="hidden" id="new_width" name="new_width" value="150">
	<input type="hidden" id="new_height" name="new_height" value="150">
	<input type="hidden" id="num" name="num" value="1">
	<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vipschool/VipschoolPage/uploadFile')?>">
	<input type="hidden" id="delete_url" name="delete_url" value="<?php echo U('Vipschool/VipschoolPage/deleteObject')?>">
	<input type="hidden" id="img_width" name="img_width" value="30%">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt"><font color=red>*</font>教师姓名：</td>
			<td><input type="text" id="realname" name="realname" value="<?php echo $teacherInfo['realname']?>" placeholder="输入教师姓名..." size="50" ></td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教师图片：</td>
			<td>
				<div>
					<span id="upload_focus_1" class="upload"></span>
					<span id="view_focus_1" class="view_file">
					<?php if($teacherInfo['img']):?>
						<img src="<?php echo $teacherInfo['show_img']?>"> <a href='#none' onclick="del_file('<?php echo $teacherInfo['img']?>','#view_focus_1','#focus_1','#upload_focus_1_msg','<?php echo U('Vipschool/VipschoolPage/deleteObject')?>')">删除图片</a>
					<?php endif;?>
					</span>
					<input type="hidden" id="focus_1" name="focus[]" value="<?php echo $teacherInfo['img']?>">
					<div id="upload_focus_1_msg"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color=red>*</font>学&nbsp;&nbsp;&nbsp;&nbsp;部：</td>
			<td>
			<?php if(!empty($gradeList)):?>
				<?php foreach ($gradeList as $key=>$grade):?>
				<input type="radio" id="grade" name="grade" value="<?php echo $grade['gid']?>" onclick="get_subjects(this.value,'<?php echo U('Vipschool/VipschoolPage/getSubjectList')?>')" <?php if($teacherInfo['gid']==$grade['gid']):?>checked<?php endif;?>><?php echo $grade['title']?>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php endforeach;?>
			<?php endif;?>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>学&nbsp;&nbsp;&nbsp;&nbsp;科：</td>
			<td id="subjectHmml">
			<?php if($teacherInfo['sid']):?>
				<input type="radio" id="subject" name="subject" value="<?php echo $teacherInfo['sid']?>" checked="checked"><?php echo $teacherInfo['subject']?>
			<?php endif;?>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>教师寄语：</td>
			<td>
				<textarea id="send_word" name="send_word" rows="2" cols="100"><?php echo $teacherInfo['send_word']?></textarea>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>教&nbsp;&nbsp;龄：</td>
			<td><input type="text" id="of_educate_age" name="of_educate_age" value="<?php echo $teacherInfo['of_educate_age']?>" size="5">年</td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>教师简介：</td>
			<td>
				<textarea id="intro_content" name="intro_content" rows="3" cols="100"><?php echo $teacherInfo['intro_content']?></textarea>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>授课风格：</td>
			<td>
				<textarea id="teaching_style" name="teaching_style" rows="3" cols="100"><?php echo $teacherInfo['teaching_style']?></textarea>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>教学心得：</td>
			<td>
				<textarea id="experience_content" name="experience_content" rows="3" cols="100"><?php echo $teacherInfo['experience_content']?></textarea>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>家长评价：</td>
			<td>
				<textarea id="comment" name="comment" rows="3" cols="100"><?php echo $teacherInfo['comment']?></textarea>
	        </td>
	    </tr>
	    <tr>
			<td class="alt"><font color=red>*</font>是否离职：</td>
			<td>
				<input type="radio" id="is_onjob1" name="is_onjob" value="1" <?php if($teacherInfo['is_onjob']==1):?>checked<?php endif;?> >在职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" id="is_onjob2" name="is_onjob" value="0" <?php if($teacherInfo['is_onjob']==0):?>checked<?php endif;?>>已离职
	        </td>
	    </tr>
		<tr><td>&nbsp;</td>
			<td>
			<input type="submit" name="save_close" class="btn" value="确认修改" id="submit">
			</td></tr>
	</table>
	</form>
</div>
</div>
</body>
</html>