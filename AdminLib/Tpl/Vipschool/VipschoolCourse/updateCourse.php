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
		'fileSizeLimit' : '3072KB',
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
			}
		},
		messages: {
			course_name: {
				required: '请填写课程名称',
				maxlength: '课程名称长度不能超过30个字'
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
	<h2>编辑课程</h2>
	<form id="add_course" name="add_course" method="POST" enctype="multipart/form-data"  action="<?php echo U('vipschool/vipschool_course/updateCourse',array('id'=>$id));?>" onsubmit="return check_course_form()">
	<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vipschool/VipschoolPage/uploadFile')?>">
	<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vipschool/VipschoolPage/deleteObject')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>课程名称： </td>
			<td>
				<input type="text" id="course_name" name="course_name" value="<?php echo $courseInfo['course_name']?>" size="60"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>课程缩略图： </td>
			<td valign="top">
				<span id="upload_course_img"></span>
					<span id="view_course_img">
					<?php  echo $courseInfo['course_img'] != ''?"<img src='".$courseInfo['show_course_img']."' width='190' height='105' />":''?>
					
					</span>
					<input type="hidden" name="course_img" id="course_img" value="<?php echo $courseInfo['course_img'] != ''?$courseInfo['course_img']:'';?>">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>关键字： </td>
			<td>
				<input type="text" value='<?php echo $courseInfo['keywords']?>' id='keywords' name='keywords' size='50'><br/>
				<br/>(多个关键词之间用英文的逗号（,）分隔，建议80字以内。)
				<label id="kkeywords_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>描述:</td>
			<td>
				<textarea id="desc" name="desc" cols="70" rows="5" placeholder="请输入课程描述..."><?php echo $courseInfo['desc'];?></textarea>(描述建议160字以内。)
				<label id="desc_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>学部： </td>
			<td>
				<?php foreach($gradeList as $key=>$grade){?>
					<input <?php echo $grade['gid'] == $courseInfo['gid']?"checked='checked'":''; ?> type="radio" name="grade" id="grade<?php echo $grade['gid'];?>" value="<?php echo $grade['gid'];?>" title="<?php echo $grade['title'];?>" onclick="select_course_info('subject',this.value,'','','#subject_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')"><?php echo $grade['title'];?>&nbsp;&nbsp;
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>学科： </td>
			<td id="subject_div">
				<?php if($courseInfo['sid']){ 
					foreach($subjectList as $key=>$subject){
				?>
					<input onclick="select_course_info('classify','','<?php echo $subject['id'];?>','','#classify_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')" type="radio" <?php echo $subject['id'] == $courseInfo['sid']?"checked='checked'":''; ?> value="<?php echo $subject['id']?>" name="subject" /><?php echo $subject['title']; ?>
				<?php 	
					}
				}else{?>
					<span>请选择学部</span>
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>分类： </td>
			<td id="classify_div">
				<?php if($courseInfo['cid'] && !empty($classifyList)){ 
					foreach($classifyList as $key=>$classify){
				?>
					<input onclick="select_course_info('twoClassify','','<?php echo $courseInfo['sid'];?>','<?php echo $classify['id'];?>','#twoClassify_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')" type="radio" <?php echo $classify['id'] == $courseInfo['cid']?"checked='checked'":''; ?> value="<?php echo $classify['id']?>" name="classify" /><?php echo $classify['title']; ?>
				<?php 	
					}
				}else{?>
					<span>请选择学科</span>
					<input type="hidden" id="classify" name="classify" value="">
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>二级分类： </td>
			<td id="twoClassify_div">
				<?php if($courseInfo['cid_two']){ 
					foreach($twoClassifyList as $key=>$classify){
				?>
					<input onclick="select_course_info('threeClassify','','','<?php echo $classify['id'];?>','#threeClassify_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')" type="radio" <?php echo $classify['id'] == $courseInfo['cid_two']?"checked='checked'":''; ?> value="<?php echo $classify['id']?>" name="twoClassify" /><?php echo $classify['title']; ?>
				<?php 	
					}
				}else{?>
					<span>请选择分类</span>
					<input type="hidden" id="twoClassify" name="twoClassify" value="">
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt">三级分类： </td>
			<td id="threeClassify_div">
				<?php if($courseInfo['cid_three']){ 
					foreach($threeClassifyList as $key=>$classify){
				?>
					<input onclick="select_course_info('fourClassify','','','<?php echo $classify['id'];?>','#fourClassify_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')"  type="radio" <?php echo $classify['id'] == $courseInfo['cid_three']?"checked='checked'":''; ?> value="<?php echo $classify['id']?>" name="threeClassify" /><?php echo $classify['title']; ?>
				<?php 	
					}
				}else{?>
					<span>请选择二级分类</span>
					<input type="hidden" id="threeClassify" name="threeClassify" value="">
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt">四级分类： </td>
			<td id="fourClassify_div">
				<?php if($courseInfo['cid_four']){ 
					foreach($fourClassifyList as $key=>$classify){
				?>
					<input  type="radio" <?php echo $classify['id'] == $courseInfo['cid_four']?"checked='checked'":''; ?> value="<?php echo $classify['id']?>" name="fourClassify" /><?php echo $classify['title']; ?>
				<?php 	
					}
				}else{?>
					<span>请选择三级分类</span>
					<input type="hidden" id="fourClassify" name="fourClassify" value="">
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top"><font color="red">*</font>主讲老师： </td>
			<td valign="top">
				<!--<input onclick="select_teacher('#tid','<?php echo U('vipschool/vipschool_course/select_teacher_by_gid_sid');?>')" type="button" value="点击选择相应老师">&nbsp;&nbsp;-->
				<?php if($courseInfo['tid']){
					?>
					<select id='tid' name='tid'>
						<?php 
						foreach($teacherList as $key=>$teacher){
						?>
							<option  <?php echo $teacher['tid'] == $courseInfo['tid']?"selected='selected'":''; ?> value="<?php echo $teacher['tid']?>" /><?php echo $teacher['realname']; ?></option>
						<?php 	
						}
						?>
						</select>
						<?php 
				 }else{?>
					<select id='tid' name='tid'>
						<option value="">该课程暂无老师</option>
					</select>
				<?php }?>
				<label id="tid_msg" class="error"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>下线时间： </td>
			<td>
				<!--  <input value="<?php echo $courseInfo['endtime'];?>"  size='20' type="text" onclick="WdatePicker({dateFmt:'yyyy-M-d H:mm:ss'})" class="Wdate"  id="endtime" name="endtime" size="10"  >
				-->
				<input type="text" value="<?php echo $courseInfo['endtime'];?>" size = '10' name='endtime' id='endtime' />&nbsp;天
			</td>
		</tr>
		<tr>
			<td class="alt" valign="top">价格： </td>
			<td  valign="top">
				<input type="radio" name="is_free" <?php echo $courseInfo['is_free'] == 1?"checked='checked'":''; ?> onclick="price_input(this.value)" value="1">免费 &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="is_free" <?php echo $courseInfo['is_free'] == 0?"checked='checked'":''; ?>  onclick="price_input()" value="0">
				<input type='text' name='price' id='price' value='<?php echo $courseInfo['price']; ?>' onclick="select_isFree_radio()" size='10' >(单位:元)
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