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
<style type="text/css">
input[type="text"] {
    height: 20px;
    line-height: 20px;
    width: 150px;
}
</style>
</head>
<body>
<div region="center">
	<div id="main">
	<h2>添加新项目</h2>
	<div class="Snav center">
		<li ref="model1" id="step1" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_one',array('mid'=>$mid))?>'"<?php endif;?>>1. 设置用户需填写的信息</li>
		<li  ref="model2" id="step2" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_two',array('mid'=>$mid))?>'"<?php endif;?>>2. 设置模块属性</li>
		<li ref="model3" id="step3" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_three',array('mid'=>$mid))?>'"<?php endif;?>>3. 设置场次属性 </li>
		<li class="hover" ref="model4" id="step4" >4.获取代码</li>
	</div>
	<div class="clearit"></div>	
	<div class="model3 model center html" id="preview">
		<div class="left">
			<h3>您已配置成功，页面预览如下:</h3>
			<div class="moveHtml">
				<ul>
				<?php echo htmlspecialchars_decode($dataArr['html']);?>
				<?php echo '<script type="text/javascript">'.$dataArr['js'].'</script>';?>
				</ul>
			</div>
		</div>
		<div class="right">
			<h3>Js 代码</h3>
			<div >
			<textarea cols="95" rows="12" id="jsHtml">
<script type="text/javascript" id="getForm">
$(document).ready(function(){
	$.ajax({
		url: "<?php echo $js_src;?>",
		type: 'get',
		dataType: 'jsonp',
		success:function (data2) {
			document.write(data2.html);
			var varScript = document.createElement("script");
			varScript.type = "text/javascript";
			varScript.text = data2.js;
			document.body.appendChild(varScript);
		}
	});
})
</script></textarea></div>
			<h3>html 代码</h3>
			<div class="pre"><textarea cols="93" rows="40" id="formHtml"><?php echo $dataArr['html']."<script type=\"text/javascript\">".$dataArr['js']."</script>";?></textarea></div>
			<div ><input type="button" class="btn" value="复制" onclick="copy_clip('#formHtml')">&nbsp;&nbsp;<a href="<?php echo U('Modular/ModularApply/step_one',array('mid'=>$mid))?>">重新配置</a></div>
		</div>
		
	</div>
</div>
</div>
</body>
</html>