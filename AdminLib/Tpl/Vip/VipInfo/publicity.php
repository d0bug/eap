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
	$("#publicity_form").validate({
		rules: {
			avatar: {
				required: true
			},
			teacher_name: {
				required: true,
				minlength: 2,
				maxlength: 30
			},
			gender: {
				required: true
			},
			rank: {
				required: true
			},
			'subject[]': {
				required: true
			},
			'grade[]': {
				required: true
			},
			'education[]': {
				required: true
			},
			'style[]': {
				required: true
			},
			'school[]': {
				required: true
			},
			send_word: {
				required: true,
				maxlength: 1000
			},
			intro_img: {
				required: true,
			},
			intro_content: {
				required: true,
				maxlength: 1000
			},
			teach_img: {
				required: true,
			},
			teach_content: {
				required: true,
				maxlength: 1000
			},
			achievement_content: {
				required: true,
				maxlength: 1000
			},
			experience_img: {
				required: true,
			},
			experience_content: {
				required: true,
				maxlength: 1000
			},
			comment:{
				required: true,
				maxlength: 1000
			}
		},
		messages: {
			avatar: {
				required: '请上传教师头像'
			},
			teacher_name: {
				required: '请填写教师姓名',
				minlength: '教师姓名不能少于2个字符',
				maxlength: '教师姓名不能超过30个字符'
			},
			gender: {
				required: '请选择教师性别'
			},
			rank: {
				required: '请选择教师级别'
			},
			'subject[]': {
				required: '请选择学科'
			},
			'grade[]': {
				required: '请选择授课年级'
			},
			'education[]': {
				required: '请选择教师资质'
			},
			'style[]': {
				required: '请选择教师风格'
			},
			'school[]': {
				required: '请选择授课校区'
			},
			send_word: {
				required: '请填写教师寄语',
				maxlength: '教师寄语内容须在1000字之内'
			},
			intro_img: {
				required: '请上传个人简介图片',
			},
			intro_content: {
				required: '请填写个人简介内容',
				maxlength: '个人简介内容须在1000字之内'
			},
			teach_img: {
				required: '请上传教课风采图片'
			},
			teach_content: {
				required: '请填写教课风采内容',
				maxlength: '教课风采内容须在1000字之内'
			},
			achievement_content: {
				required: '请填写教课成果内容',
				maxlength: '教课成果内容笔须在1000字之内'
			},
			experience_img: {
				required: '请上传教学心得图片',
			},
			experience_content: {
				required: '请填写教学心得内容',
				maxlength: '教学心得内容笔须在1000字之内'
			},
			comment:{
				required: '请填写评论内容',
				maxlength: '评论内容不能超过1000字'

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
		submitHandler: function(form) {
			var arr = get_form_vars();
			$.post("<?php echo U('Vip/VipInfo/update_publicity',array('userKey'=>$userKey))?>",
			{avatar:arr['avatar'],teacher_name:arr['teacher_name'],gender:arr['gender'],rank:arr['rank'],subject:arr['subject'],grade:arr['grade'],send_word:arr['send_word'],intro_img:arr['intro_img'],intro_content:arr['intro_content'],teach_img:arr['teach_img'],teach_content:arr['teach_content'],achievement_content:arr['achievement_content'],experience_img:arr['experience_img'],experience_content:arr['experience_content'],comment:arr['comment'],preview_url:$("#preview_url").val(),status:0,education:arr['education'],style:arr['style'],school:arr['school']},
			function(data){
				if(data == 1){
					alert('网络宣传信息修改成功,请耐心等待审核……');
				}else{
					alert('网络宣传信息修改失败');
				}
			}
			);
		}
	});
});
</script>
</head>
<body>
<div region="center">
<div id="main">
	<h2>网络宣传信息修改</h2>
	<div id="return"></div>
	<form id="publicity_form" name="publicity_form" method="POST"  >
	<input type="hidden" id="preview_url" name="preview_url" value="">
	<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt"><font color=red>*</font>姓　名：</td>
			<td colspan="2">
				<input type="text" id="teacher_name" name="teacher_name" value="<?php if($publicityInfo['teacher_name']):?><?php echo $publicityInfo['teacher_name'];?><?php endif?>">
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>性　别：</td>
			<td colspan="2">
				<input type="radio" name="gender" id="gender" value="f" <?php if('f' == trim($publicityInfo['gender'])):?>checked<?php endif?> >男&nbsp;&nbsp;
				<input type="radio" name="gender" id="gender" value="m" <?php if('m' == trim($publicityInfo['gender'])):?>checked<?php endif?> >女
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>头　像：</td>
			<td class="no-border-right">
				<input type="file" name="file_upload_avatar" id="file_upload_avatar" />
				<div id="view_img_one">
				<?php if($publicityInfo['avatar']):?>
					<div class="pic"><img src="<?php echo $publicityInfo['avatar_show'];?>"></div>
					<a href="#" onclick="del_img('<?php echo $publicityInfo['avatar'];?>','#view_img_one','#avatar','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a>
				<?php endif?>
				</div>
				<input type="hidden" name="avatar" id="avatar" value="<?php if($publicityInfo['avatar']):?><?php echo $publicityInfo['avatar'];?><?php endif?>">
			</td>
			<td width="60%" class="no-border-left">注：<br>
					1. 头像尺寸为200*200，必须为pose照<br>
					2. 可以多次上传，最多可传1张头像<br>
					3. 图片大小不要大于3兆 单位(M / 1M=1024KB 1KB=1024B)<br></td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教师级别：</td>
			<td  colspan="2">
			<?php foreach($rankArr as $key=>$rank):?>
				<input type="radio" name="rank" id="rank" value="<?php echo $rank;?>" <?php if(empty($publicityInfo['rank']) && $key ==0):?>checked<?php elseif($rank == trim($publicityInfo['rank'])):?>checked<?php endif?> ><?php echo $rank;?>&nbsp;&nbsp;
			<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>学　科：</td>
			<td  colspan="2">
				<?php foreach($subjectArr as $key=>$subject):?>
					<input type="checkbox" name="subject[]" id="subject_<?php echo $key?>" value="<?php echo $subject;?>" <?php if(strpos($publicityInfo['subject'],$subject.',')!==false):?>checked<?php endif?> ><?php echo $subject;?>&nbsp;&nbsp;
				<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>主讲年级：</td>
			<td colspan="2">
				<?php foreach($gradesArr as $key=>$grade):?>
				<input type="checkbox" name="grade[]" id="grade_<?php echo $key?>" value="<?php echo $key;?>" <?php if(strpos(','.$publicityInfo['grades'],','.$key.',')!==false):?>checked<?php endif?> ><?php echo $grade;?>&nbsp;&nbsp;
				<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教师资质：</td> 
			<td colspan="2">
				<?php foreach($educationList as $key=>$education):?>
				<input type="checkbox" name="education[]" id="education_<?php echo $key?>" value="<?php echo $education['id'];?>" <?php if(strpos(','.$publicityInfo['edu_id_list'],','.$education['id'].',')!==false):?>checked<?php endif?> ><?php echo $education['title'];?>&nbsp;&nbsp;
				<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>授课风格：</td> 
			<td colspan="2">
				<?php foreach($styleList as $key=>$style):?>
				<input type="checkbox" name="style[]" id="style_<?php echo $key?>" value="<?php echo $style['id'];?>" <?php if(strpos(','.$publicityInfo['style_id_list'],','.$style['id'].',')!==false):?>checked<?php endif?> onclick="checkStyleNum('style_<?php echo $key?>')"><?php echo $style['title'];?>&nbsp;&nbsp;
				<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>授课校区：</td> 
			<td colspan="2">
				<input type="checkbox" id="checkall">全选&nbsp;&nbsp;
				<?php foreach($schoolList as $key=>$school):?>
				<input type="checkbox" name="school[]" id="school_<?php echo $key?>" value="<?php echo $school['id'];?>" <?php if(strpos(','.$publicityInfo['school_id_list'],','.$school['id'].',')!==false):?>checked<?php endif?> ><?php echo $school['title'];?>&nbsp;&nbsp;
				<?php endforeach?>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教师寄语：</td>
			<td colspan="2">
				<textarea id="send_word" name="send_word" cols="90" rows="3"><?php if($publicityInfo['send_word']):?><?php echo $publicityInfo['send_word'];?><?php endif ?></textarea>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>个人简介图片：</td>
			<td class="no-border-right">
			<input type="file" name="file_upload_intro_img" id="file_upload_intro_img" />
			<!-- <a href="javascript:$('#file_upload_intro_img').uploadify('upload','*');">上传</a> -->
			<div id="view_img_two">
				<?php if($publicityInfo['intro_img']):?>
					<div class="pic"><img src="<?php echo $publicityInfo['intro_img_show'];?>"></div>
					<a href="#" onclick="del_img('<?php echo $publicityInfo['intro_img'];?>','#view_img_two','#intro_img','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a>
				<?php endif?>
			</div>
			<input type="hidden" name="intro_img" id="intro_img" value="<?php if($publicityInfo['intro_img']):?><?php echo $publicityInfo['intro_img'];?><?php endif ?>">
			</td>
			<td class="no-border-left">注：<br>1. 头像尺寸为190*160，必须为黑板照<br>2. 可以多次上传，最多可传1张头像<br>3. 图片大小不要大于3兆 单位(M / 1M=1024KB 1KB=1024B)</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>个人简介内容：</td>
			<td colspan="2">
				<textarea id="intro_content" name="intro_content" cols="90" rows="3"><?php if($publicityInfo['intro_content']):?><?php echo $publicityInfo['intro_content'];?><?php endif ?></textarea>
			</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教课风采图片：</td>
			<td class="no-border-right">
			<input type="file" name="file_upload_teach_img" id="file_upload_teach_img" />
			<!-- <a href="javascript:$('#file_upload_teach_img').uploadify('upload','*');">上传</a> -->
			<div id="view_img_three">
				<?php if($publicityInfo['teach_img']):?>
					<div class="pic"><img src="<?php echo $publicityInfo['teach_img_show'];?>"></div>
					<a href="#" onclick="del_img('<?php echo $publicityInfo['teach_img'];?>','#view_img_three','#teach_img','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a>
				<?php endif?>
			</div>
			<input type="hidden" name="teach_img" id="teach_img" value="<?php if($publicityInfo['teach_img']):?><?php echo $publicityInfo['teach_img'];?><?php endif ?>">
			</td>
			<td class="no-border-left">注：<br>1. 头像尺寸为190*160，必须为学生合影或生活照<br>2. 可以多次上传，最多可传1张头像<br>3. 图片大小不要大于3兆 单位(M / 1M=1024KB 1KB=1024B)</td>
		</tr>
		<tr><td class="alt"><font color=red>*</font>教课风采内容：</td>
			<td colspan="2">
				<textarea id="teach_content" name="teach_content" cols="90" rows="3"><?php if($publicityInfo['teach_content']):?><?php echo $publicityInfo['teach_content'];?><?php endif ?></textarea>
			</td>
		</tr>

		<tr><td class="alt"><font color=red>*</font>教学成果内容：</td>
			<td colspan="2">
				<textarea id="achievement_content" name="achievement_content" cols="90" rows="3"><?php if($publicityInfo['achievement_content']):?><?php echo $publicityInfo['achievement_content'];?><?php endif ?></textarea>
			</td>
		</tr>

		<tr><td class="alt"><font color=red>*</font>教学心得图片：</td>
			<td class="no-border-right">
			<input type="file" name="file_upload_experience_img" id="file_upload_experience_img" />
			<!-- <a href="javascript:$('#file_upload_experience_img').uploadify('upload','*');">上传</a> -->
			<div id="view_img_four">
				<?php if($publicityInfo['experience_img']):?>
					<div class="pic"><img src="<?php echo $publicityInfo['experience_img_show'];?>"></div>
					<a href="#" onclick="del_img('<?php echo $publicityInfo['experience_img'];?>','#view_img_four','#experience_img','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a>
				<?php endif?>
			</div>
			<input type="hidden" name="experience_img" id="experience_img" value="<?php if($publicityInfo['experience_img']):?><?php echo $publicityInfo['experience_img'];?><?php endif ?>">
			</td>
			<td class="no-border-left">注：<br>1. 头像尺寸为190*160，必须为生活照<br>2. 可以多次上传，最多可传1张头像<br>3. 图片大小不要大于3兆 单位(M / 1M=1024KB 1KB=1024B)</td>
		</tr>

		<tr><td class="alt"><font color=red>*</font>教学心得内容：</td>
			<td colspan="2">
				<textarea id="experience_content" name="experience_content" cols="90" rows="4"><?php if($publicityInfo['experience_content']):?><?php echo $publicityInfo['experience_content'];?><?php endif ?></textarea>
			</td>
		</tr>

		<tr><td class="alt"><font color=red>*</font>大家都评论：</td><td colspan="2">
		<textarea id="comment" name="comment" cols="80" rows="6"><?php echo $publicityInfo['comment']?></textarea><br></td></tr>
		<tr><td></td><td colspan="2"><button type="submit" class="btn">提交</button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="preview('<?php echo U('Vip/VipInfo/preview')?>')">预览</a></td></tr>
	</table>
	</form>
	</div>
</div>
</body>
</html>
<script type="text/javascript">
$("#checkall").click(function(){
	//判断选择状态
	if($(this).attr('checked'))
	{
		//选中状态
		$(this).siblings().each(function(){
			$(this).attr('checked',true);
		})
	}else
	{
		//取消状态
		$(this).siblings().each(function(){
			$(this).attr('checked',false);
		})
	}
	
});
</script>