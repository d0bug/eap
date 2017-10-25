<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center" >
<div id="main">
	<form id="CircleOperate" name="CircleOperate" method="POST"  action="<?php echo U('Vip/VipMyask/setMyaskStatus');?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>是否前台显示： </td>
			<td>
				<input type="radio" id="status" name="status" value="1" <?php if($AskInfo['status'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="status" name="status" value="0" <?php if($AskInfo['status'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><input type="hidden" id="askid" name="askid" value="<?php echo $AskInfo['id']?>">
			    <input type="submit" class="btn" value="确认提交"></td>
		</tr>
	</table>
	</form>
	<br><br>
</div>
</div>
</body>
</html>