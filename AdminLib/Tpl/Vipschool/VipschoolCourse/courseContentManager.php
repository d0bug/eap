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
	$("#courseContent_form").validate({
		rules: {
		<?php if($handout != 1){?>
			video_name: {
				required: true
			},
			cc_vid:{
				required:true,
				//url:true
			},
			try_time:{
				number:true
			},
		<?php }?>	
			<?php if($video != 1){?>
				handout_name:{
					required: true
				},
				handout_url:{
					required:true
				}
			<?php }?>
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
				//url:'请输入合法的地址格式'
			},
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
	$("#allow_try").click(function(){
		if($("#allow_try").attr("checked") == 'checked'){
			$("#try_time").val('').focus();
		}
	})
	$("#try_time").focus(function(){
		$("#allow_try").attr("checked",'checked');
	})

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
			//if(obj.status == 1){
				//alert(obj.url);alert(obj.show_url);
				$("#handout_url").val(obj.url);
				$("#upload_file_url").html(obj.url);
				$("#del_file_url").html("<a href=\"#none\" onclick=\"del_file('"+obj.url+"','"+obj.del_url+"')\">删除</a>");
			//}
		}
	});
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
	<form id="courseContent_form" name="courseContent_form" method="POST" action="<?php echo U('vipschool/vipschoolCourse/courseContentManager',array('course_id'=>$course_id)); ?>"  enctype="multipart/form-data" >
	
	<?php if($handout != 1){ ?>
	<h2>视频内容</h2>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo" >
				<tr><td class="alt"><font color=red>*</font>视频名称：</td>
					<td>
						<input type="text" id="video_name" name="video_name" value="" placeholder="请输入视频名称" size="50" >&nbsp;&nbsp;
						<input type="checkbox" id="allow_try"  value="1" name="allow_try">允许试看&nbsp;&nbsp; 
						<input type="text" size="10" id='try_time' value='' name="try_time">&nbsp;分钟
					</td>
				</tr>
				<tr><td class="alt"><font color=red>*</font>视频文件ID：</td>
					<td><input name='cc_vid' id='cc_vid' type="text" value="" size="80">(请将视频传至 CC视频后台，然后将相应ID粘贴在此处。)</td>
				</tr>
				<tr>
					<td class="alt">知识点</td>
					<td>
						<select name='knowlege' id='knowlege'>
							<option selected='selected'>请选择知识点</option>
							<?php foreach($knowlegeArr as $key=>$knowlege){?>
								<option value="<?php echo $knowlege['kid'];?>" ><?php echo $knowlege['kname'];?></option>
							<?php }?>
						</select>
						<input type="hidden" value="" name='knowlege_name' id="knowlege_name" />	
					</td>
			    </tr>
			</table>
	<?php }?>	
		<?php if($video != 1){?>
	<h2>讲义内容</h2>	
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr><td class="alt"><font color=red>*</font>讲义名称：</td>
				<td>
					<input type="text" id="handout_name" name="handout_name" value="" placeholder="请输入讲义名称" size="50" >&nbsp;&nbsp;
				</td>
			</tr>
			<tr><td class="alt"><font color=red>*</font>讲义文件：</td>
				<td>
					<div>
					<input type="hidden" id="upload_jiangyi_url" value="<?php echo U("Vipschool/VipschoolPage/uploadFile");?>" />
					<span id="upload_jiangyi" class="upload"></span>
					<span id="upload_file_url" class="view_file"></span>&nbsp;&nbsp;&nbsp;
					<span id="upload_file_msg" class="view_file"></span>&nbsp;&nbsp;&nbsp;
					<span id="del_file_url" class="view_file"></span>
					<input type="hidden" id="handout_url" name="handout_url" value="">
				</div>
				</td>
			</tr>
			
		</table>
		<?php }?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
				    <input type="submit" class="btn" value="确认提交">
				</td>
			</tr>
		</table>
	</form>
</div>
</div>
</body>
</html>