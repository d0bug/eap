<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/courseClassify.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#update_form").validate({
		rules: {
			video_name: {
				required: true
			},
			try_time:{
				number:true
			},
			cc_vid:{
				required:true,
			}
		},
		messages: {
			
			video_name: {
				required: '请填写视频名称'
			},
			try_time:{
				number:'请填写数字'
			},
			cc_vid:{
				required:'请填写视频的ID',
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
	$("#allow_try").click(function(){
		if($("#allow_try").attr("checked") == 'checked'){
			$("#try_time").val('').focus();
		}
	})
	$("#try_time").focus(function(){
		$("#allow_try").attr("checked",'checked');
	})
	$("#knowlege").change(function(){
		var knowlege_name =$(this).find("option:selected").text();
		$("#knowlege_name").val(knowlege_name);
	})
	
})
</script>
</head>
<body>
<div region="center">
<div id="main">
	<h2>修改视频内容</h2>
	<form id="update_form" name="update_form" method="POST" action="<?php echo U('vipschool/vipschoolCourse/updateVideo',array('course_id'=>$course_id,'vid'=>$vid)); ?>"  enctype="multipart/form-data" >
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt"><font color=red>*</font>视频名称：</td>
				<td>
					<input type="text" id="video_name" name="video_name" value="<?php echo $videoInfo['video_name']; ?>" placeholder="请输入视频名称" size="50" >&nbsp;&nbsp;
					<input type="checkbox" id="allow_try" <?php echo $videoInfo['allow_try']?"checked='checked'":'';?>  value="1" name="allow_try">允许试看&nbsp;&nbsp; 
					<input type="text" size="10" id='try_time' value='<?php echo $videoInfo['try_time'];?>' name="try_time">&nbsp;分钟
				</td>
			</tr>
			<tr><td class="alt"><font color=red>*</font>视频文件ID：</td>
				<td><input name='cc_vid' id='cc_vid' type="text" value="<?php echo $videoInfo['cc_vid'];?>" size="80">(请将视频传至 CC视频后台，然后将相应ID粘贴在此处。)</td>
			</tr>
			<tr>
				<td class="alt">知识点</td>
				<td>
					<select name='knowlege' id='knowlege'>
						<option selected='selected'>请选择知识点</option>
						<?php foreach($knowlegeArr as $key=>$knowlege){?>
							<option <?php echo $videoInfo['knowlege_id'] == $knowlege['kid']?"selected='selected'":'' ;?> value="<?php echo $knowlege['kid'];?>" ><?php echo $knowlege['kname'];?></option>
						<?php }?>
					</select>	
					<input type="hidden" value="<?php echo $videoInfo['knowlege_name'];?>" name='knowlege_name' id="knowlege_name" />
				</td>
		    </tr>
		    <tr>
				<td class="alt">&nbsp;</td>
				<td>
				    <input type="submit" class="btn" value="确认修改">
				</td>
			</tr>
		</table>
	</form>
</div>
</div>
</body>
</html>