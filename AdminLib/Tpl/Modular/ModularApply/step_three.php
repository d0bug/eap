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
function attrmsubmit(){
	var inp = $("input[class='attrm']");
	var status = true;
	for (var i=1;i<=inp.length;i++){
		if(inp.eq(i).val() == 'undefined' || inp.eq(i).val()=='' ){
			status = false;
			alert('属性名称'+i+'为必填项');
			return false;
		}
	}

}
</script>
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
		<li class="hover" ref="model3" id="step3" >3. 设置场次属性</li>
		<li ref="model4" id="step4" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_four',array('mid'=>$mid))?>'"<?php endif;?>>4. 获取代码</li>
	</div>
	<div class="clearit"></div>	
	<div id="szmk" class="center model2 model">
		<form  method="post" name="form3" id="form3" enctype="multipart/form-data"  action="<?php echo U('Modular/ModularApply/savadata_step_three')?>" onsubmit="return attrmsubmit();">
			<input type="hidden" name="model3" value="true"> 
			<input type="hidden" id="mid" name="mid" value="<?php echo $mid?>">
			<div id="d">
				<ul class='UlformT' style="text-align:left;">
					<li idx="0"><span style="font-weight:bold;font-size:20px;width:100px;">属性0:</span><br>
					<span style="width:100px;">属性名称:&nbsp;&nbsp;</span><input type="text" name="attrname[0]"  id="attrname0"  class="attrm" style="width:200px;">&nbsp;&nbsp;<input type="file" name="namelist[0]" id="namelist0">注意：上传的是excel文件<br>
                                        <s><span style="width:100px;">数据0:&nbsp;&nbsp;</span><input type="text"   name="data_name[0][]" id="data_name0" value="请输入名称!" onfocus="if(this.value=='请输入名称!'){this.value=''}" onblur="if(this.value==''){this.value='请输入名称!'}"  style="width:200px;">&nbsp;&nbsp;<input type="text" name="limitnum[0][]" id="limitnum0" value="请输入限制人数!" onfocus="if(this.value=='请输入限制人数!'){this.value=''}" onblur="if(this.value==''){this.value='请输入限制人数!'}" style="width:200px;">&nbsp;&nbsp;</s><input class="btn" type="button" value="增加数据" onclick="addAbIpt(this)"><span style="color:red;">注意：限制人数无可不填</span>
					</li>
				</ul>
			</div>
			<p style="text-align:center;"><input class="btn" type="button" id="b" value="增加属性">&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn" type="submit" value="保存数据"></p>
		</form>
	</div>
</div>
</div>
</body>
<script language="javascript">
i = 1;
document.getElementById("b").onclick=function(){
    $("#d").append('<div id="div_'+i+'"><ul class="UlformT" style="text-align:left;"><li idx="' +i+ '"><span style="font-weight:bold;font-size:20px;width:100px;">属性'+i+':</span><br><span style="width:100px;">属性名称'+i+':&nbsp;&nbsp;</span><input type="text" name="attrname['+i+']"  id="attrname'+i+'"   class="attrm"  style="width:200px;">&nbsp;&nbsp;<input type="file" name="namelist['+i+']" id="namelist'+i+'" value=""><s><br /><span style="width:100px;">数据0:&nbsp;&nbsp;</span><input type="text"   name="data_name['+i+'][]" id="data_name0"  style="width:200px;">&nbsp;&nbsp;<input type="text" name="limitnum['+i+'][]" id="limitnum0" style="width:200px;">&nbsp;&nbsp;<input type="button" value="删除" class="btn" onclick="delAbIpt(this)"/>&nbsp;&nbsp;</s><input class="btn" type="button" id="da" value="增加数据" onclick="addAbIpt(this)"></li></ul><br><input type="button" class="btn" value="删除"  onclick="del('+i+')" style="text-align:center;"/></div>');
  i = i + 1;
}

function addAbIpt(el){
    var p = el.parentNode;
    var ii = p.getElementsByTagName('s').length;
    var idx = p.getAttribute('idx');
    $(p).append('<s><br /><span style="width:100px;">数据'+ii+':&nbsp;&nbsp;</span><input name="data_name['+idx+'][]" id="data_name'+ii+'" type="text" /><input type="text" name="limitnum['+idx+'][]" id="limitnum'+ii+'" style="width:200px;"><input type="button" value="删除按钮" class="btn" onclick="delAbIpt(this)" /></s>');
}
function delAbIpt(el){
    var p = el.parentNode;
    if (p.parentNode.getElementsByTagName('s').length > 0)
        p.parentNode.removeChild(p);
    else
        p.parentNode.parentNode.removeChild(p.parentNode);
}

function del(o){
 document.getElementById("d").removeChild(document.getElementById("div_"+o));
}
</script>

</html>
