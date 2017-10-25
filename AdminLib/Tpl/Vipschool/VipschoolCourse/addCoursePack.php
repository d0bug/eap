<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/courseClassify.js"></script>
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<script type="text/javascript">
$(document).ready(function() {

	$("#add_course_pack").validate({
		rules: {
			pname: {
				required: true,
				maxlength: 30
			},
			cid:{
				required: true,
			},
			'course_id[]':{
				required: true,
			},
			ptype:{
				required: true,
			},
			coupon_type:{
				required: true,
			},
			price: {
				required:true,
				number:true
			},
			'coupon_value[]':{
				number:true
			},
			introduce:{
				required:true
			}
		},
		messages: {
			pname: {
				required: '请填写打包名称',
				maxlength: '课程名称长度不能超过30个字'
			},
			cid:{
				required: '请选择打包分类',
			},
			'course_id[]':{
				required: '请选择包含课程',
			},
			ptype:{
				required: '请选择课程类型',
			},
			coupon_type:{
				required: '请选择价格优惠类型',
			},
			price: {
				required:'请输入课程包原价',
				number:'请输入数字'
			},
			'coupon_value[]':{
				number:'请输入数字'
			},
			introduce:{
				required:'请填写课程包介绍'
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
	
	$("input[name='coupon_type']").click(function(){
		var val = $(this).val();
		if(val == 0){
			$("#coupon_value0").focus();
		}else{
			$("#coupon_value1").focus();
		}
	})
	
	$("input[name='coupon_type']").focus(function(){
		var val = $(this).val();
		$("#coupon_value_"+val).focus();
	})
	
	$("input[name='coupon_value']").focus(function(){
		var id = $(this).attr("id").split("_").pop();
		$("#coupon_type"+id).attr("checked",true);
	})
	
	
	$("input[name='ptype']").click(function(){
		var val = $(this).val();
		if(val == 1){
			$("#course_num").focus();
		}
	})
	
})
</script>
<body>
<div region="center">
<div id="main">
	<h2>添加课程包</h2>
	<form id="add_course_pack" name="add_course_pack" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vipschool/VipschoolCourse/addCoursePack');?>" onsubmit="return check_add_course_pack()">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>打包名称： </td>
			<td>
				<input type="text" id="pname" name="pname" value="" size="60" >
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>打包分类： </td>
			<td>
				<select name='cid' id="cid" onchange="change_course_list(this.value,'<?php echo U('Vipschool/VipschoolCourse/changeCourseList')?>')">
				<option value="">请选择分类名称</option>
				<?php 
					foreach($classifyList as $key=>$value){
				?>
						<option value='<?php echo $value['cid'] ?>'><?php echo $value['title']; ?></option>
				<?php 
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>包含课程： </td>
			<td>
				<select name="course_id[]" style="width:100" size="10" multiple="multiple" id="course_id" >
					<?php foreach($courseList as $key=>$course){
						?>
						<option value="<?php echo $course['id'];?>"><?php echo $course['course_name'];?><?php if($course['price']):?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $course['price'];?>元<?php endif;?></option>
						<?php 
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>课程类型:</td>
			<td>
				<input type="radio"  name='ptype' id='ptype0' value='0'  >课程已完&nbsp;&nbsp;
				<input type="radio" name='ptype' id='ptype1' value='1'>课程预售&nbsp;&nbsp;共<input type="text" id="course_num" name="course_num" value="" size="5">课<label id="course_num_msg"></label>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>价格优惠： </td>
			<td>
				<input type="radio" name='coupon_type' id='coupon_type0' value='0'>减免&nbsp;
					<input size='10' type="text" name='coupon_value[]' id='coupon_value_0' value="" />&nbsp;&nbsp;
				<input type="radio" name='coupon_type' id='coupon_type1' value='1'>折扣&nbsp;
					<input size='10' type="text" name='coupon_value[]' id='coupon_value_1' value="" />%
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>原价： </td>
			<td id="subject_div">
				<input type="text" value="" name="price" id="price" size='10' /> <input type="button" value="计算优惠后价格" onclick="get_last_price()"><label id="last_price"></label>
			</td>
		</tr>
		<tr>
			<td class="alt">教材： </td>
			<td id="classify_div">
				<input type="checkbox" value="1" name="is_give_book" id="is_give_book" />赠送教材
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>课程包介绍：</td>
			<td id="classify_div">
				<textarea id="introduce" name="introduce" cols="80" rows="5"></textarea>
			</td>
		</tr>
		
		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			    <input type="submit" class="btn" value="确认提交" onclick="return check_add_course_pack()"c>
			</td>
		</tr>
	</table>
	</form>
</div>
</div>
</body>
</html>