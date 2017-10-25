<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/courseClassify.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<!--  <script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>-->
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<script type="text/javascript">
$(function(){
	$('#upload_course_img').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_url").val(),
		'method'   : 'post',
		'formData':{preview:1,width:560,height:310,type:'img',is_realname:1,autocut:1,id:new Date()},
		'buttonText' : '点击上传课程缩略图',
		'width':'200',
		'multi'    : false,
		'fileTypeDesc' : 'Image Files',
		'fileTypeExts' : '*.gif; *.jpg; *.png',
		'fileSizeLimit' : '60KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#course_img").val(obj.url);
			$("#view_course_img").html("<img src="+obj.show_url+" width=\"190\" height=\"105\">&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_object('"+obj.url+"','#view_course_img','#course_img','"+$("#del_url").val()+"')\">删除</a>");
		}
	});
})
$(document).ready(function() {
	$("#add_course").validate({
		rules: {
			course_name: {
				required: true,
				maxlength: 30
			},
			course_img:{
				required: true
			},
			keywords:{
				required: true
			},
			desc:{
				required: true
			},
			grade:{
				required: true
			},
			subject:{
				required: true
			},
			classify:{
				required: true
			},
			
			tid:{
				required: true
			},
			price: {
				number:true
			},
			endtime:{
				required:true,
				number:true,
				min:0
			},
			is_free:{
				required:true,
			}
		},
		messages: {
			course_name: {
				required: '请填写课程名称',
				maxlength: '课程名称长度不能超过30个字'
			},
			course_img:{
				required: '请上传课程缩略图'
			},
			keywords:{
				required: '请填写课程关键字'
			},
			desc:{
				required: '请填写课程介绍'
			},
			grade:{
				required: '请选择学部'
			},
			subject:{
				required: '请选择学科'
			},
			classify:{
				required: '请选择一级分类'
			},
			
			tid:{
				required: '请选择主讲教师'
			},
			price: {
				number:'请输入数字'
			},
			endtime:{
				required:'请输入下线时间',
				number:'请输入数字',
				min:'请输入整数'
			},
			is_free:{
				required:'请选择课程是否免费',
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
<body>
<div region="center">
<div id="main">
	<h2>添加课程</h2>
	<form id="add_course" name="add_course" method="POST" enctype="multipart/form-data"  action="<?php echo U('vipschool/vipschool_course/uploadCourse',array('auto_close'=>$auto_close));?>" onsubmit="return check_course_form()">
	<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vipschool/VipschoolPage/uploadFile')?>">
	<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vipschool/VipschoolPage/deleteObject')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>课程名称： </td>
			<td>
				<input type="text" id="course_name" name="course_name" value="" size="60"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>课程缩略图： </td>
			<td valign="top">
				<div>
				<span id="upload_course_img" class="upload"></span>
					<span id="view_course_img">
					</span>
					<input type="hidden" name="course_img" id="course_img" value="">
					（<font color=red>注意:课程图片宽度560px，高度310px，图片小于60KB</font>）
				</div>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>关键字： </td>
			<td>
				<input type="text" value='' id='keywords' name='keywords' size='50'><br/>
				<br/>(多个关键词之间用英文的逗号（,）分隔，建议80字以内。)
				<label id="kkeywords_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>描述:</td>
			<td>
				<textarea id="desc" name="desc" cols="70" rows="5" placeholder="请输入课程描述..."></textarea>(描述建议160字以内。)
				<label id="desc_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>学部： </td>
			<td>
				<?php foreach($gradeList as $key=>$grade){?>
					<input type="radio" name="grade" id="grade<?php echo $grade['gid'];?>" value="<?php echo $grade['gid'];?>" title="<?php echo $grade['title'];?>" onclick="select_course_info('subject',this.value,'','','#subject_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')"><?php echo $grade['title'];?>&nbsp;&nbsp;
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>学科： </td>
			<td id="subject_div">
				<span>请选择学部</span>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>分类： </td>
			<td id="classify_div">
				<span>请选择学科</span>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red"></font>二级分类： </td>
			<td id="twoClassify_div">
				<span>请选择分类</span>
			</td>
		</tr>
		<tr>
			<td class="alt">三级分类： </td>
			<td id="threeClassify_div">
				<span>请选择二级分类</span>
			</td>
		</tr>
		<tr>
			<td class="alt">四级分类： </td>
			<td id="fourClassify_div">
				<span>请选择三级分类</span>
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top"><font color="red">*</font>主讲老师： </td>
			<td valign="top">
				<!--<input onclick="select_teacher('#tid','<?php echo U('vipschool/vipschool_course/select_teacher_by_gid_sid');?>')" type="button" value="点击选择相应老师">&nbsp;&nbsp;-->
				<select id='tid' name='tid'>
					<option value="">请选择主讲教师</option>
				</select>
				<label id="tid_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>下线时间： </td>
			<td>
				<!--  <input size='20' type="text" onclick="WdatePicker({dateFmt:'yyyy-M-d H:mm:ss',minDate:'%y-%M-#{%d}'})" class="Wdate"  id="endtime" name="endtime" value="" size="10"  >
					-->
				<input type="text" value="" size = '10' name='endtime' id='endtime' />&nbsp;天	
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top">价格： </td>
			<td  valign="top">
				<input type="radio" name="is_free"  onclick="price_input(this.value)" value="1">免费 &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="is_free"  onclick="price_input()" value="0">
				<input type='text' name='price' id='price' value='' onclick="select_isFree_radio()" size='10' >(单位:元)
			</td>
		</tr>
		
		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			    <input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value="">
			    <input type="submit" class="btn" value="确认提交">
			</td>
		</tr>
	</table>
	</form>
</div>
</div>
</body>
</html>