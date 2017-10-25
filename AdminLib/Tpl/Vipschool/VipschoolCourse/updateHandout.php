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
	$("#updateHandout").validate({
		rules: {
			handout_name:{
				required: true
			},
			handout_url:{
				required:true
			}
		},
		messages: {
			
			handout_name:{
				required: '请填写讲义名称'
			},
			handout_url:{
				required:'请填写讲义的名称'
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

	$('#upload_jiangyi').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_jiangyi_url").val(),
		'method'   : 'post',
		'formData':{'preview':1,type:'file'},
		'buttonText' : '选择讲义文件',
		'width':'200',
		'multi'    : true,
		'fileTypeDesc' : 'doc docx pdf',
		'fileTypeExts' : '*.doc;*.docx;*.pdf',
		'fileSizeLimit' : '153600KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#upload_file_msg").html(obj.status);
			if(obj.success == 1){
				$("#handout_url").val(obj.url);
				$("#upload_file_url").html(obj.url);
				$("#del_file_url").html("<a href=\"#none\" onclick=\"del_file('"+obj.url+"','"+obj.del_url+"')\">删除</a>");
			}
		}
	});
	
})
</script>
</head>
<body>
<div region="center">
<div id="main">
	<h2>讲义内容</h2>
	<form id="updateHandout_form" name="updateHandout_form" method="POST" action="<?php echo U('vipschool/vipschoolCourse/updateHandout',array('course_id'=>$course_id,'hid'=>$hid)); ?>"  enctype="multipart/form-data" >
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt"><font color=red>*</font>讲义名称：</td>
				<td>
					<input type="text" id="handout_name" name="handout_name" value="<?php echo $handoutInfo['handout_name'];?>" placeholder="请输入讲义名称" size="50" >&nbsp;&nbsp;
				</td>
			</tr>
			<tr><td class="alt"><font color=red>*</font>讲义文件：</td>
				<td>
					<div>
					<input type="hidden" id="upload_jiangyi_url" value="<?php echo U("Vipschool/VipschoolCourse/uploadFile");?>" />
					<span id="upload_jiangyi" class="upload"></span>
					<span id="upload_file_url" class="view_file"><?php echo $handoutInfo['handout_url'];?></span>&nbsp;&nbsp;&nbsp;
					<span id="upload_file_msg" class="view_file"></span>&nbsp;&nbsp;&nbsp;
					<span id="del_file_url" class="view_file"></span>
					<input type="hidden" id="handout_url" name="handout_url" value="<?php echo $handoutInfo['handout_url'];?>">
				</div>
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