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

function checkEndTime(){
	var startTime="<?php echo $heluInfo['lesson_date'];?> "+$("#lesson_begin").val()+":00";
	var start=new Date(startTime.replace("-", "/").replace("-", "/"));
	var endTime="<?php echo $heluInfo['lesson_date'];?> "+$("#lesson_end").val()+":00";
	var end=new Date(endTime.replace("-", "/").replace("-", "/"));
	if(end<=start){
		$("#time_msg").show();
		$("#time_msg").html('课程结束时间必须大于开始时间');
		return false;
	}else{
		$("#time_msg").hide();
	}
	return true;
}
$(function() {
	$('#submitButton').click(function() {
		if(check_helu_form()){
			submitHelu(1);
		}

	});
	$('#saveButton').click(function() {
		if(check_helu_form()){
			submitHelu(0);
		}
	});
});
function  check_helu_form(){
	if($('#student_name').val() == '' || $('#lesson_begin').val() == '' || $('#lesson_end').val() == ''){
		alert('非法操作');
		return false;
	}
	if($('#lesson_topic').val() == ''){
		alert('课次主题不能为空');
		return false;
	}
	if($('#lesson_topic').val().length >80){
		alert('课次主题不能超过80字');
		return false;
	}
	if($('#comment').val() == ''){
		alert('课堂评价不能为空');
		return false;
	}
	if($('#comment').val().length <70){
		alert('课堂评价不能小于70字');
		return false;
	}
	if($('#itembank_score').val() !== '' ){
		if (!/^\d+$/.test($('#itembank_score').val())) {
			alert('测试卷评分必须为数字');
			return false;
		}
	}
	if(!checkEndTime()){
		return false;
	}
	return true;

}
function submitHelu(is_sendsms){
	var handoutsUrlStr = '';
	$('input[name="handouts_url[]"]').each(function(){
		if(this.value!='' && this.value!='undefined'){
			handoutsUrlStr = handoutsUrlStr+this.value+'|';
		}
	});
	var itembankUrlStr = '';
	$('input[name="itembank_url[]"]').each(function(){
		if(this.value!='' && this.value!='undefined'){
			itembankUrlStr = itembankUrlStr+this.value+'|';
		}
	});
	$.post('<?php echo U('Vip/VipStudents/doHelu')?>',
	{act:'<?php echo $act?>',id:$("#id").val(),is_sendsms:is_sendsms,helu_id:$("#helu_id").val(),kecheng_code:$("#kecheng_code").val(),lesson_no:$("#lesson_no").val(),student_code:$("#student_code").val(),student_name:$("#student_name").val(),lesson_date:$("#lesson_date").val(),lesson_begin:$("#lesson_begin").val(),lesson_end:$("#lesson_end").val(),lesson_topic:$("#lesson_topic").val(),comment:$("#comment").val(),handouts_url:handoutsUrlStr,itembank_url:itembankUrlStr,itembank_score:$("#itembank_score").val(),is_send_sms:$('#is_send_sms').val()},
	function(data){
		var obj = eval('(' + data + ')');
		alert(obj.msg);
		if(obj.status == 1){
			window.parent.closeWindow(1);
		}
		location.reload();
	}
	);
}
</script>
</head>
<body >
<div region="center" >
<div id="main">
	<?php if($act == 'select'):?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr><td colspan="2" class="f_20">课时基本信息：</td></tr>
				<tr>
					<td >学员姓名：<?php echo $heluInfo['student_name'];?></td>
					<td>上课时间：<?php echo $heluInfo['lesson_date'];?> <?php echo $heluInfo['lesson_begin'];?>- <?php echo $heluInfo['lesson_end'];?>&nbsp;&nbsp;&nbsp;&nbsp;课次：<?php echo $heluInfo['lesson_no'];?></td>
				</tr>
				<tr><td colspan="2">上课主题：<?php echo $heluInfo['lesson_topic'];?></td></tr>
				<tr><td colspan="2" valign="top">课堂评价：<?php echo $heluInfo['comment'];?></td></tr>
				<tr>
					<td valign="top" colspan="2">测试卷上传： 
						<div class="left_60">
<!-- 							讲义文档:
							<span id="view_handouts_file" class="view_file">
							<?php if($heluInfo['handouts_url']):?>
								<a href="#none"><?php echo $heluInfo['handouts_url_show'];?></a>&nbsp;&nbsp;
							<?php endif;?>
							</span><br><br> -->
							测试卷文档:
							<?php if($heluInfo['itembank_url']):?>
								<a href="#none"><?php echo $heluInfo['itembank_url_show'];?></a>&nbsp;&nbsp;
							<?php endif;?>
							</span><br><br>
							测试卷得分：<?php echo !empty($heluInfo['itembank_score'])?$heluInfo['itembank_score']:'';?>
						</div>
					</td>
				</tr>
			</table>
	<?php else:?>
		<form id="helu" name="helu" method="POST" enctype="multipart/form-data">
			<input type="hidden" id="is_send_sms" name="is_send_sms" value="<?php echo $heluInfo['is_send_sms'];?>">
			<input type="hidden" id="helu_id" name="helu_id" value="<?php echo $heluInfo['helu_id'];?>">
			<input type="hidden" id="id" name="id" value="<?php echo $heluInfo['id'];?>">
			<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
			<input type="hidden" id="delimg_url" name="delimg_url" value="<?php echo U('Vip/VipInfo/del_img')?>">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr><td colspan="2" class="f_20">课时基本信息：
						<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $heluInfo['kecheng_code'];?>">
						<input type="hidden" id="lesson_no" name="lesson_no" value="<?php echo $heluInfo['lesson_no'];?>">
						<input type="hidden" id="student_code" name="student_code" value="<?php echo $heluInfo['student_code'];?>">
					</td>
				</tr>
				<tr>
					<td >学员姓名：<?php echo $heluInfo['student_name'];?><input type="hidden" id="student_name" name="student_name" value="<?php echo $heluInfo['student_name'];?>"></td>
					<td>上课时间：<?php echo $heluInfo['lesson_date'];?> <input type="hidden" id="lesson_date" name="lesson_date" value="<?php echo $heluInfo['lesson_date'];?>">
								<?php echo $heluInfo['lesson_begin'];?>-<?php echo $heluInfo['lesson_end'];?>
								<input type="hidden" id="lesson_begin" name="lesson_begin" value="<?php echo $heluInfo['lesson_begin'];?>" size="5">
								<input type="hidden" id="lesson_end" name="lesson_end" value="<?php echo $heluInfo['lesson_end'];?>" size="5" ><span class="error" id="time_msg"></span>&nbsp;&nbsp;&nbsp;&nbsp;
								课次：<?php echo $heluInfo['lesson_no'];?>
					</td>
				</tr>
				<tr>
					<td  colspan="2">上课主题：<input type="text" id="lesson_topic" name="lesson_topic" value="<?php echo $heluInfo['lesson_topic'];?>" size="60"><font color="red">*</font></td>
				</tr>
				<tr>
					<td colspan="2" valign="top">课堂评价：
				<?php if(!empty($heluInfo['comment'])): ?>
					<textarea id="comment" name="comment" rows="20" cols="80"><?php echo $heluInfo['comment'];?></textarea>
				<?php else: ?>
					<textarea id="comment" name="comment" rows="20" cols="80"><?php echo date('Y/m/d',strtotime($heluInfo['lesson_date'])) ?> <?php echo $userInfo['real_name']?>老师课堂评价:<?php echo $heluInfo['comment'];?></textarea>
				<?php endif; ?>
					<font color="red">*</font> </td>
				</tr>
				<!--<tr>
					<td colspan="2" valign="top"><input type='checkbox' value='1' name='isSendMsg' id='isSendMsg' checked='checked' >给家长发送短信<span style='color:red'>(勾选此项后，系统会将课程评价以短信形式发送给家长)</span></td>
				</tr>-->
				<tr>
					<td valign="top" colspan="2">测试卷上传： 
						<div class="left_60">
<!-- 							<span id="upload_handouts">讲义文档</span>
							<input type="hidden" id="handouts_count" name="handouts_count" value="<?php echo $heluInfo['handouts_count']?>">
							<span id="preview_handouts">
							<?php if($heluInfo['handouts']):?>
								<?php foreach($heluInfo['handouts'] as $k=>$file):?>
								<span id="view_handouts_file_<?php echo $k?>" class="view_file">
								<a href="#none"><?php echo $file['url_show'];?></a>&nbsp;&nbsp;
								<a href="#none" onclick="del_img('<?php echo $file['url'];?>','#view_handouts_file_<?php echo $k?>','#handouts_url_<?php echo $k?>','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><br>
								</span><label id="handouts_msg_<?php echo $k?>" class="success"></label>
								<input type="hidden" id="handouts_url_<?php echo $k?>" name="handouts_url[]" value="<?php echo $file['url'];?>" size="100">
								<?php endforeach?>
							<?php endif;?>
							</span> -->
							
							<span id="upload_itembank"></span><label id="itembank_msg" class="success"></label><br>
							<input type="hidden" id="itembank_count" name="itembank_count" value="<?php echo $heluInfo['itembank_count']?>">
							<span id="preview_itembank">
							<?php if($heluInfo['itembank']):?>
								<?php foreach($heluInfo['itembank'] as $k=>$file):?>
								<span id="view_itembank_file_<?php echo $k?>" class="view_file">
								<a href="#none"><?php echo $file['url_show'];?></a>&nbsp;&nbsp;
								<a href="#none" onclick="del_img('<?php echo $file['url'];?>','#view_itembank_file_<?php echo $k?>','#itembank_url_<?php echo $k?>','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a><br>
								</span><label id="itembank_msg_<?php echo $k?>" class="success"></label>
								<input type="hidden" id="itembank_url_<?php echo $k?>" name="itembank_url[]" value="<?php echo $file['url'];?>" size="100">
								<?php endforeach?>
							<?php endif;?>
							</span>
							<!--<span id="view_itembank_file" class="view_file">
							<?php if($heluInfo['itembank_url']):?>
								<a href="#none"><?php echo $heluInfo['itembank_url_show'];?></a>&nbsp;&nbsp;
								<a href="#none" onclick="del_img('<?php echo $heluInfo['itembank_url'];?>','#view_itembank_file','#itembank_url','<?php echo U('Vip/VipInfo/del_img')?>')">删除</a>
							<?php endif;?>
							</span>
							<input type="hidden" id="itembank_url" name="itembank_url" value="<?php echo $heluInfo['itembank_url'];?>"><br>
							-->
							测试卷得分：<input type="text" id="itembank_score" name="itembank_score" value="<?php echo !empty($heluInfo['itembank_score'])?$heluInfo['itembank_score']:'';?>" size="6">
							<div class="t_right">&nbsp;</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="alt" colspan="2"><!--<?php if($heluInfo['id']):?><input type="submit" class="btn" value="确认修改" onclick="checkEndTime()"><?php else:?><input type="submit" class="btn" value="核录课时" onclick="checkEndTime()"><?php endif;?>-->
						<!--<div style="font-weight: 400;color:red">注意：每节课的课评仅可发送一次短信</div>-->
						<input type="button" class="btn" id="saveButton" value="保存" onclick="checkEndTime()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<!--<input type="button" class="<?php if($heluInfo['is_send_sms'] == 1):?>gray-btn<?php else:?>btn<?php endif;?>"  id="submitButton" value="提交并发送短信" onclick="checkEndTime()" <?php if($heluInfo['is_send_sms'] == 1):?>disabled="disabled" style="color:#ffffff"<?php endif;?>>-->
					</td>
				</tr>
			</table>
		</form>
	<?php endif;?>
</div>
</div>
</body>
</html>