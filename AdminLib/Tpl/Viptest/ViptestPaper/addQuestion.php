<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/viptest.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#addQuestion").validate({
		rules: {
			img: {
				required: true
			},
			paper_id: {
				required: true
			},
			module_id: {
				required: true
			},
			option_num: {
				required: true,
				digits:true,
				max:7
			},
			answer: {
				required: true
			},
			time_limit: {
				required: true,
				digits:true
			},
			accuracy: {
				required: true,
				digits:true
			},
			seq: {
				required: true,
				digits:true
			},
		},
		messages: {
			img: {
				required: '请上传题干图片'
			},
			paper_id: {
				required: '非法操作，试卷归属丢失'
			},
			module_id: {
				required: '请选择所属模块'
			},
			option_num: {
				required: '请填写选项数量',
				digits:'选项数量格式错误',
				max:'选项数量最多为7个'
			},
			answer: {
				required: '请选择正确答案'
			},
			time_limit: {
				required: '请填写限定时间',
				digits:'限定时间格式错误'
			},
			accuracy: {
				required: '请填写虚拟正确率',
				digits:'虚拟正确率格式错误'
			},
			seq: {
				required: '请填写试题排序',
				digits:'试题排序格式错误'
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
		submitHandler: function(form) {
			$.post("<?php echo U('Viptest/ViptestPaper/addQuestion')?>",
			{id:$('#id').val(),img:$('#img').val(),paper_id:$('#paper_id').val(),module_id:$('#module_id').val(),option_num:$('#option_num').val(),answer:$('#answer').val(),time_limit:$('#time_limit').val(),accuracy:$('#accuracy').val(),seq:$('#seq').val()},
			function(data){
				if(data == 1){
					alert('操作成功');
					location.reload();
				}else{
					alert('操作失败');
				}
			}
			);
		}
	});
})
</script>
</head>
<body >
<div region="center" >
<div id="main">
		<form id="addQuestion" name="addQuestion" method="POST" enctype="multipart/form-data">
			<input type="hidden" id="id" name="id" value="<?php echo $_REQUEST['id']?>">
			<input type="hidden" id="paper_id" name="paper_id" value="<?php echo empty($_REQUEST['id'])?$_REQUEST['paper_id']:$questionInfo['paper_id'];?>">
			<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Viptest/ViptestPaper/uploadImg')?>">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr>
					<td valign="top" colspan="2"><font color=red>*</font>题干图片：
						<div style="margin-left:60px">
						<input type="file" name="file_upload_img" id="file_upload_img" />
						<div id="view_img">
							<?php if($questionInfo['img']):?>
								<div class="pic"><img src="<?php echo $questionInfo['show_url'];?>" width="200" height="150"></div>
								<a href="#" onclick="del_img('<?php echo $questionInfo['img'];?>','#view_img','#img','<?php echo U('Viptest/ViptestPaper/delImg')?>')">删除</a>
							<?php endif?>			
						</div>
						</div> 
						<input type="hidden" id="img" name="img" value="<?php echo $questionInfo['img'];?>">
					</td>
				</tr>
				<!--<tr><td colspan="2" ><font color=red>*</font>
						所属试卷：<select id="paper_id" name="paper_id" onchange="get_moduleList(this.value,'<?php echo $getModuleUrl?>')">
								<?php foreach($paperList as $key=>$paper):?>
									<option value="<?php echo $paper['id']?>" <?php if($_GET['paper_id']==$paper['id']||$questionInfo['paper_id']==$paper['id']):?>selected<?php endif;?>><?php echo $paper['title']?></option>
								<?php endforeach?>
								</select>
					</td>
				</tr>-->
				<tr><td colspan="2" ><font color=red>*</font>
						所属模块：<select id="module_id" name="module_id">
								<?php foreach($moduleList as $key=>$module):?>
									<option value="<?php echo $module['id']?>" <?php if($_GET['module_id']==$module['id']||$questionInfo['module_id']==$module['id']):?>selected<?php endif;?>><?php echo $module['name']?></option>
								<?php endforeach?>
								</select>
					</td>
				</tr>
				<tr><td colspan="2" ><font color=red>*</font>选项数量：<input type="text" id="option_num" name="option_num" value="<?php echo !empty($questionInfo['option_num'])?$questionInfo['option_num']:'4';?>" size="10" onblur="get_answerArr(this.value,'<?php echo U('Viptest/ViptestPaper/json_answerArr')?>')">个选项</td></tr>
				<tr><td colspan="2" ><font color=red>*</font>
					正确答案：<select id="answer" name="answer">
								<?php foreach($answerArr as $key=>$answer):?>
									<option value="<?php echo $key?>" <?php if($questionInfo['answer']==$key):?>selected<?php endif;?>><?php echo $answer?></option>
								<?php endforeach?>
							 </select>
					</td>
				</tr>
				<tr><td colspan="2" ><font color=red>*</font>限定时间：<input type="text" id="time_limit" name="time_limit" value="<?php echo $questionInfo['time_limit']?>" size="5">秒</td></tr>
				<tr><td colspan="2" ><font color=red>*</font>虚拟正确率：<input type="text" id="accuracy" name="accuracy" value="<?php echo $questionInfo['accuracy']?>" size="5">%</td></tr>
				<tr><td colspan="2" ><font color=red>*</font>排序：<input type="text" id="seq" name="seq" value="<?php echo $defaultSeq;?>" size="5"></td></tr>
				<tr>
					<td colspan="2">
					<?php if($_GET['id']):?>
						<input type="submit" class="btn" value="确认修改" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="<?php echo U('Viptest/ViptestPaper/deleteQuestion',array('id'=>$_GET['id']))?>" class="blue" onclick="return confirm('删除该试题将同时删除该试题的所有答题记录，\n确认要删除该试题吗？')">删除该试题</a>
					<?php else:?>
						<input type="submit" class="btn" value="保存" >
					<?php endif;?>
					</td>
				</tr>
			</table>
		</form>
</div>
</div>
</body>
</html>