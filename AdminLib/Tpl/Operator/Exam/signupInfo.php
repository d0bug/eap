<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<style type="text/css">
		fieldset{height:120px;font-size:12px}
		ul{margin:0px;padding:0px}
		li{font-weight:bold;padding:5px;border-bottom:1px dashed #ccc;width:45%;float:left;margin-left:10px;list-style-type:none;font-size:12px}
		li span{font-weight:normal}
	</style>
</head>
<body>
<ul>
<li><span>考生姓名：</span><?php echo $stuInfo['sname']?></li>
<li><span>准考证号：</span><?php echo $signupInfo['exam_code']?></li>
<li><span>报名考点：</span><?php echo $posInfo['pos_caption']?></li>
<li><span>考点电话：</span><?php echo $posInfo['pos_telephone']?></li>
<li><span>所在考场：</span>第<?php echo sprintf('%02d', $signupInfo['room_num'])?>考场</li>
<li><span>考场座位：</span>第<?php echo sprintf('%03d', $signupInfo['seat_num'])?>号</li>
<li style="width:92%"><span>考点地址：</span><?php echo $posInfo['pos_addr']?></li>
<!--li style="width:92%"><input type="button" value="发送报名信息短信" onclick="alert('未开通')"/></li-->
<li style="width:92%;border:none"><span style="float:left">交通路线：</span><div style="float:left;"><?php echo $posInfo['pos_bus']?></div></li>

</ul>
</body>
</html>