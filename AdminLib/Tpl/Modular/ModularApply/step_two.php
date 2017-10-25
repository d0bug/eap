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
	$("#form2").validate({
		submitHandler: function(form) {
			//判断附加条件是否为空
			var isdisplay = $("input[name=display]:checked").val();
			if(isdisplay == 1){
				if($("#persontext").val()==''){
					$("#persontext_msg").html('请填写文案内容');
					return false;
				}
			}
			var limited = $("input[name=limited]:checked").val();
			if(limited == 1){
				if(($('#start').val() == '' || $('#start').val() == 0) && ($('#end').val() == '' || $('#end').val() == 0)){
					$("#limited_msg").html('请填写限制人数');
					return false;
				}
			}
			var message = $("input[name=message]:checked").val();
			if(message == 1){
				if($('#mess').val() == '' ){
					$("#mess_msg").html('请填写短信内容');
					return false;
				}
			}
			$("#form2").submit();
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
		<li  ref="model1" id="step1" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_one',array('mid'=>$mid))?>'"<?php endif;?>>1. 设置用户需填写的信息</li>
		<li ref="model2" id="step2" class="hover" >2. 设置模块属性</li>
		<li  ref="model3" id="step3" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_three',array('mid'=>$mid))?>'"<?php endif;?>>3.设置场次属性</li>
		<li  ref="model4" id="step4" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_four',array('mid'=>$mid))?>'"<?php endif;?>>4. 获取代码</li>
	</div>
	<div class="clearit"></div>	
	<div id="szmk" class="center model2 model">
		<form  method="post" name="form2" id="form2" action="<?php echo U('Modular/ModularApply/savedata_step_two')?>" >
			<input type="hidden" name="model2" value="true"> 
			<input type="hidden" id="mid" name="mid" value="<?php echo $mid?>">
			<ul class='UlformT'>
			<li><span>显示已预约或报名人数:</span>	<input type="radio" name="display" <?php echo empty($moduleInfo['display']) ? 'checked="checked"': '' ;?> value="0" onclick="hide('#yes_display')"> 不显示 <input type="radio" name="display" <?php echo !empty($moduleInfo['display']) ? 'checked="checked"': '' ;?> value="1"  onclick="show('#yes_display')"> 显示	
				<label id="yes_display" style="display:<?php if($moduleInfo['display']==1):?>inline<?php else:?>none<?php endif;?>">文案：
				<select name="persontext" id="persontext">
					<option value="">请选择文案</option>
					<?php foreach($markedWords as $key=>$word):?>
					<option value="<?php echo $word;?>" <?php if($moduleInfo['words']==$word):?>selected<?php endif;?> ><?php echo $word;?></option>
					<?php endforeach?>
				</select>
				<span id="persontext_msg" class="error"></span>
				</label>
			</li>
			
			<li><span>限制预约或报名人数:</span>
				<input type="radio" name="limited" checked="checked" value="0" onclick="hide('#yes_limited')" <?php echo empty($moduleInfo['limitshow']) ? 'checked="checked"': '' ;?>> 不限制 
				<input type="radio" name="limited"  value="1"  onclick="show('#yes_limited')"  <?php echo !empty($moduleInfo['limitshow']) ? 'checked="checked"': '' ;?>> 限制
				<span id="yes_limited" style="display:<?php if($moduleInfo['limitshow'] == 1 &&$moduleInfo['limited']):?>inline<?php else:?>none<?php endif;?>;">
				<input class="xperson" type="text" name="start" id="start" value="<?php echo $moduleInfo['start'];?>" />人 - <input class="xperson" name="end" id="end" type="text" value="<?php echo $moduleInfo['end'];?>" />人
				<span id="limited_msg" class="error"></span></span>
			</li>
			<li><span>限制金卡会员报名/预约:</span>
				<input type="radio" name="isgoldlimit" checked="checked" value="0" <?php echo empty($moduleInfo['isgoldlimit']) ? 'checked="checked"': '' ;?>> 不限制 
				<input type="radio" name="isgoldlimit"  value="1" <?php echo !empty($moduleInfo['isgoldlimit']) ? 'checked="checked"': '' ;?>> 限制
			</li>
			
			<li><span>触发短信:</span>	
				<input type="radio" name="message" checked="checked" value="0"  onclick="hide('#yes_message')" <?php echo empty($moduleInfo['message']) ? 'checked="checked"': '' ;?>> 不触发 
				<input type="radio" name="message"  value="1"  onclick="show('#yes_message')" <?php echo !empty($moduleInfo['message']) ? 'checked="checked"': '' ;?>> 触发	
			</li>
			<li id="yes_message" style="display:<?php if($moduleInfo['message']==1):?>inline<?php else:?>none<?php endif;?>;"><span>&nbsp;</span>
			<textarea class="textarea" name="mess" id="mess"><?php echo $moduleInfo['messagetext'];?></textarea><span id="mess_msg" class="error"></span>
			<!--<p><font color=red>*请输入短信内容，将需要根据实际数据替换的部分用##括出来。</font></p>--->
			<p>替换字段匹配：姓名:#name，性别：#sex，学校：#school，年级：#grade，学科：#subject，邮箱：#email，手机号：#phone，留言：#message</p>
			
			</li>
			</ul>
			<p><input class="btn" type="submit" value="保存配置"><span id="model2_msg"><?php echo $error;?></span></p>
		</form>
	</div>
</div>
</div>
</body>
</html>