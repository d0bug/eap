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
<script type="text/javascript" src="/static/js/eval.js"></script>
</head>
<body >
<div region="center" >
<div id="main">
		<form id="whole_level" name="whole_level" method="POST">
			<input type="hidden" id="id" name="id" value="<?php echo $LevelInfo['id']?>">	   
			<p><font color=red>*</font>整体评级标准:<input type=hidden id="level_num" name="level_num" value="<?php count($level_num);?>">
			<a href="#" onclick="add_wholelevel('#wholelevelSpan')"><img src="/static/images/add.png"></a>
			<div id="wholelevelSpan" style="margin-left:50px;">	
					<?php 
						if(!empty($whole_level)){
							foreach($whole_level as $key=>$v){
					?>	
					<span id="span_level_<?php echo $key+1;?>">
				   名称：<input type="text" id="level_<?php echo $key+1;?>" name="name[]" value="<?php echo $v['name']?>" size="10">&nbsp;&nbsp;
				   上限：<input type="text" id="level_<?php echo $key+1;?>" name="up[]" value="<?php echo $v['up']?>" size="10">&nbsp;&nbsp;
				   下限：<input type="text" id="level_<?php echo $key+1;?>" name="low[]" value="<?php echo $v['low']?>" size="5">%&nbsp;&nbsp;
				   说明：<input type="text" id="level_<?php echo $key+1;?>" name="intro[]" value="<?php echo $v['intro']?>" size="20">&nbsp;&nbsp;
				   <a href="#" onclick="del_wholelevel('#span_level_<?php echo $key+1;?>')"><img src="/static/images/delete.png"></a>
				   <br></span>
				   <?php
				  		 }
					}else{
				   ?>
					<span id="span_level_0">
				   名称：<input type="text" id="level_1" name="name[]" value="" size="10">&nbsp;&nbsp;
				   上限：<input type="text" id="level_1" name="up[]" value="" size="10">&nbsp;&nbsp;
				   下限：<input type="text" id="level_1>" name="low[]" value="" size="5">%&nbsp;&nbsp;
				   说明：<input type="text" id="level_1" name="intro[]" value="" size="20">&nbsp;&nbsp;
				   <br></span>   	
				   <?php
						}
				   ?>
			</div></p>
			<div align="center"><input type="submit" class="btn" value="保存" ></div>
		</form>
</div>
<script type="text/javascript">

	function add_wholelevel(divId){
		var num = Number($("#level_num").val());
		var new_num = num+1;
		var html = '<span id="span_level_'+new_num+'">';
		html += '名称：<input type="text" id="level_'+new_num+'" name="name[]" value="" size="10">&nbsp;&nbsp;';
		html += '上限：<input type="text" id="level_'+new_num+'" name="up[]" value="" size="5">&nbsp;&nbsp;';
		html += '下限：<input type="text" id="level_'+new_num+'" name="low[]" value="" size="5">&nbsp;&nbsp;';
		html += '说明：<input type="text" id="level_'+new_num+'" name="intro[]" value="" size="20">&nbsp;&nbsp;';
		html += '<a href="#" onclick="del_wholelevel(\'#span_level_'+new_num+'\')"><img src="/static/images/delete.png"></a><br></span>';
		$("#wholelevelSpan").append(html);
		$("#level_num").val(new_num);
	}

	function del_wholelevel(spanId){
		var num = Number($("#level_num").val());
		var new_num = num-1;
		$(spanId).remove();
		$("#level_num").val(new_num);
	}	

</script>
</body>
</html>