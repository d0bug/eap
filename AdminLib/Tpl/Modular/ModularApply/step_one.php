<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#form1").validate({
		rules: {
			channel: {
				required: true
			},
			modulename: {
				required: true,
				maxlength: 30
			},
		},
		messages: {
			channel: {
				required: '请选择需求频道'
			},
			modulename: {
				required: '请填写模块标题',
				maxlength: '模块标题不能超过30个字符'
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
			var remark_str = '';
			$("input[name='remark[]']:input").each(function(){
				if($(this).attr("checked")){
					remark_str += $(this).val()+"-"
				}
			})

			var remark1_str = '';
			$("input[name='remark1[]']:input").each(function(){
				if($(this).attr("checked")){
					remark1_str += $(this).val()+"-"
				}
			})
			
			if(!!$("#display3").attr('checked')){
				if(remark_str==''){
					$("#grade_msg").html('请选择年级选项');
					return false;
				}
			}
			if(!!$("#display4").attr('checked')){
				if(remark1_str==''){
					$("#subject_msg").html('请选择学科选项');
					return false;
				}
			}
			
			var type_str = '';
			$("input[name='type[]']:input").each(function(){
				type_str += $(this).val()+"-"
			})

			var title1_str = '';
			$("input[name='title1[]']:input").each(function(){
				title1_str += $(this).val()+"-"
			})

			var title2_str = '';
			$("input[name='title2[]']:input").each(function(){
				title2_str += $(this).val()+"-"
			})

			var display_str = '';
			$("input[name='display[]']:input").each(function(){
				if($(this).attr("checked")){
					display_str += $(this).val()+"-"
				}else{
					display_str += "0-"
				}
			})

			var required_str = '';
			$("input[name='required[]']:input").each(function(){
				if($(this).attr("checked")){
					required_str += $(this).val()+"-"
				}else{
					required_str += "0-"
				}
			})
			
			$.post("<?php echo U('Modular/ModularApply/savadata_step_one',array('mid'=>$mid))?>",
			{channel:$('#channel').val(),modulename:$('#modulename').val(),type_str:type_str,title1_str:title1_str,title2_str:title2_str,display_str:display_str,required_str:required_str,remark_str:remark_str,remark1_str:remark1_str},
			function(data){
				var dataObj=eval("("+data+")");
				if(dataObj.status == 0){
					$('#model1_msg').html('保存失败');
				}else{
					window.location.href=dataObj.url;
				}
			}
			);
		}
	});
})

</script>
</head>
<body>
<div region="center">
	<div id="main">
	<h2>添加新项目</h2>
	<div class="Snav center">
		<li class="hover" ref="model1" id="step1">1. 设置用户需填写的信息</li>
		<li ref="model2" id="step2" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_two',array('mid'=>$mid))?>'"<?php endif;?>>2. 设置模块属性</li>
		<li ref="model3" id="step3" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_three',array('mid'=>$mid))?>'"<?php endif;?>>3. 设置场次属性</li>
		<li ref="model4" id="step4" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_four',array('mid'=>$mid))?>'"<?php endif;?>>4. 获取代码</li>
	</div>
	<div class="clearit"></div>	
	<div id="main_container" class="center model1 model" style="display:<?php echo $modelhover1[1];?> " style="display:none">
		<form method="post" name="form1" id="form1" action="<?php echo U('Modular/ModularApply/savadata_step_one')?>" >
			<input type="hidden" name="model1" value="true">
			<ul class='Ulform'>
				<li><span>需求频道:</span>
				<select name="channel" id="channel">
					<option value="">请选择需求频道</option>
					<?php foreach($channelArr as $key=>$channel):?>
					<option value="<?php echo $key;?>" <?php if($moduleInfo['channel']==$key):?>selected<?php endif;?>><?php echo $channel;?></option>
					<?php endforeach?>
				</select></li>
				<li><span>模块标题:</span><input type="text"  name="modulename" id="modulename" value="<?php echo $moduleInfo['name'];?>" id=""></li>
			</ul>
		<table width="100%" border="1" class="tableForm" cellpadding="0" cellspacing="0">
	      <tr>
	        <td>信息类别</td>
	        <td>是否显示 <span class="font12">默认不选为不显示</span></td>
	        <td>是否必填 <span class="font12">默认不选为不必填</span></td>
	        <td>前台显示名称</td>
	        <td>显示形式</td>
	      </tr>
	      <tr>
	        <td>姓名<input type="hidden"  name="title1[]" value="1" /></td>
	        <td><input type="checkbox" name="display[]" id="display0" <?php echo (empty($mid) || !empty($moduleFormInfo[0]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,0)"></td>
	        <td><input type="checkbox" name="required[]" id="required0" <?php echo empty($moduleFormInfo[0]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,0)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[0]['title2']) ? '姓名' : $moduleFormInfo[0]['title2'];?>" /></td>
	        <td>
	        	<select >
	          		<option value="text" <?php echo empty($moduleFormInfo[0]['cate']) ? '' : 'selected="selected"';?>>文本</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="text">
	        </td>
	      </tr>
	      <tr>
	        <td>性别<input type="hidden"name="title1[]" value="2" /></td>
	        <td><input type="checkbox" name="display[]" id="display1" <?php echo (empty($mid) || !empty($moduleFormInfo[1]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,1)"></td>
	        <td><input type="checkbox" name="required[]" id="required1" <?php echo empty($moduleFormInfo[1]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,1)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[1]['title2']) ? '性别' : $moduleFormInfo[1]['title2'];?>" /></td>
	        <td>
	        	<select>
	          		<option value="radio" selected="selected" >单选框</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="radio">
	        </td>
	      </tr>
	      <tr>
	        <td>学校<input type="hidden"  name="title1[]" value="3" /></td>
	        <td><input type="checkbox" name="display[]" id="display2" <?php echo (empty($mid) || !empty($moduleFormInfo[2]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,2)"></td>
	        <td><input type="checkbox" name="required[]" id="required2" <?php echo empty($moduleFormInfo[2]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,2)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[2]['title2']) ? '学校' : $moduleFormInfo[2]['title2'];?>" /></td>
	        <td>
	        	<select>
	          		<option value="text" selected="selected" >文本</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="text">
	        </td>
	      </tr>
	      <tr>
	        <td>年级<input type="hidden" name="title1[]" value="4" /></td>
	        <td><input type="checkbox" name="display[]" id="display3" <?php echo (empty($mid) || !empty($moduleFormInfo[3]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,3)"></td>
	        <td><input type="checkbox" name="required[]" id="required3"<?php echo empty($moduleFormInfo[3]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,3)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[3]['title2']) ? '年级' : $moduleFormInfo[3]['title2'];?>" /></td>
	        <td>
	        	<select  onchange="change_hidden_type(this.value,'#grade_type')">
	           		<option value="select" <?php if($moduleFormInfo[3]['cate'] =='select'):?>selected<?php endif;?>>以下拉框形式</option>
	           		<option value="radio"  <?php if($moduleFormInfo[3]['cate'] =='radio'):?>selected<?php endif;?>>以单选按钮形式</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="<?php if($moduleFormInfo[3]['cate']=='radio'):?>radio<?php else:?>select<?php endif;?>" id="grade_type">
	        </td>
	      </tr>
	      <tr>
	        <td colspan="5"><div align="left">
				请选择显示的年级：
				<div id="grade" class="json">
	            <input type="checkbox"  id="checkAll_grade" name="checkAll_grade"  <?php if($moduleFormInfo[3]['remarkcount']==13):?>checked<?php endif;?> > 全部 
	            <?php foreach($gradeArr as $key=>$grade):?>
	            <input type="checkbox" name="remark[]" <?php if(strpos($moduleFormInfo[3]['remark'],'"'.$key.'"')):?> checked <?php endif;?> value="<?php echo $key;?>" > <?php echo $grade;?>
	            <?php endforeach?>
	            <span class="error" id="grade_msg"></span>
				</div>
	        </div></td>
	      </tr>
	      <tr>
	        <td>学科<input type="hidden" name="title1[]" value="5" /></td>
	        <td><input type="checkbox" name="display[]" id="display4" <?php echo (empty($mid) || !empty($moduleFormInfo[4]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,4)"></td>
	        <td><input type="checkbox" name="required[]" id="required4" <?php echo empty($moduleFormInfo[4]['required']) ? '' : 'checked="checked"';?>  value="1" onclick="required_to_display(this.id,4)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[4]['title2']) ? '学科' : $moduleFormInfo[4]['title2'];?>" /></td>
	        <td>
	        	<select onchange="change_hidden_type(this.value,'#subject_type')">
	         		<option value="select" <?php if($moduleFormInfo[4]['cate'] =='select'):?>selected<?php endif;?>>以下拉框形式</option>
	         		<option value="radio" <?php if($moduleFormInfo[4]['cate'] =='radio'):?>selected<?php endif;?>>以单选按钮形式</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="<?php if($moduleFormInfo[4]['cate']=='radio'):?>radio<?php else:?>select<?php endif;?>" id="subject_type">
	        </td>
	      </tr>
		   <tr> 
	        <td colspan="5">
	        <div align="left">请选择显示的学科：
			<div id="dept" class="json">
	            <input type="checkbox"  id="checkAll_subject" name="checkAll_subject"  <?php if($moduleFormInfo[4]['remarkcount']==5):?>checked<?php endif;?> > 全部 
	            <?php foreach($subjectArr as $key=>$subject):?>
				<input type="checkbox" name="remark1[]"  <?php if(strpos($moduleFormInfo[4]['remark'],'"'.$key.'"')!==false):?>checked<?php endif;?> value="<?php echo $key;?>"> <?php echo $subject;?>
				<?php endforeach?>
				 <span class="error" id="subject_msg"></span>
			</div>
			</div></td>
	      </tr>
	      <tr>
	        <td>Email<input type="hidden" name="title1[]" value="6" /></td>
	        <td><input type="checkbox" name="display[]" id="display5" <?php echo (empty($mid) || !empty($moduleFormInfo[5]['display'])) ? 'checked="checked"' : '';?> value="1" onclick="display_to_require(this.id,5)"></td>
	        <td><input type="checkbox" name="required[]" id="required5" <?php echo empty($moduleFormInfo[5]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,5)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[5]['title2']) ? 'Email' : $moduleFormInfo[5]['title2'];?>" /></td>
	        <td>
	        	<select>
	          		<option value="text" selected="selected" >文本</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="text">
	        </td>
	      </tr><tr>
	        <td>手机号<input type="hidden" name="title1[]" value="7" /></td>
	        <td><input type="checkbox" name="display[]"  id="display6" <?php echo (empty($mid) || !empty($moduleFormInfo[6]['display'])) ? 'checked="checked"' : '';?> value="1"  onclick="display_to_require(this.id,6)"></td>
	        <td><input type="checkbox" name="required[]" id="required6" <?php echo empty($moduleFormInfo[6]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,6)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[6]['title2']) ? '手机号' : $moduleFormInfo[6]['title2'];?>" /></td>
	        <td>
	        	<select>
	          		<option value="text" selected="selected" >文本</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="text">
	        </td>
	      </tr><tr>
	        <td>留言<input type="hidden" name="title1[]" value="8" /></td>
	        <td><input type="checkbox" name="display[]"  id="display7" <?php echo (empty($mid) || !empty($moduleFormInfo[7]['display'])) ? 'checked="checked"' : '';?> value="1"  onclick="display_to_require(this.id,7)"></td>
	        <td><input type="checkbox" name="required[]" id="required7" <?php echo empty($moduleFormInfo[7]['required']) ? '' : 'checked="checked"';?> value="1" onclick="required_to_display(this.id,7)"></td>
			<td><input type="text" name="title2[]" value="<?php echo empty($moduleFormInfo[7]['title2']) ? '留言' : $moduleFormInfo[7]['title2'];?>" /></td>
	        <td>
	        	<select onchange="change_hidden_type(this.value,'#message_type')">
	          		<option value="text" <?php if($moduleFormInfo[7]['cate']=='text'):?>selected<?php endif;?>>单行文本</option>
	          		<option value="textarea" <?php if($moduleFormInfo[7]['cate']=='textarea'):?>selected<?php endif;?>>多行文本</option>
	        	</select>
	        	<input type="hidden" name="type[]" value="text" id="message_type">
	        </td>
	      </tr>
	    </table>
		<p>&nbsp;</p>
		<p><button class="btn" >保存配置</button><span id="model1_msg"><?php echo $error;?></span></p>
		</form>
	</div>
</div>
</div>
</body>
</html>