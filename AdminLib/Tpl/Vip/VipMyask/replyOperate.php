<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	function check_opercircle()	{
		var title = $("#title").val();
		if(title == ''){
			alert('标题不能为空');
			return false;
		}
	}
</script>
</head>
<body >
<div region="center" >
<div id="main">
	<h2>提问问题操作</h2>
	<form id="CircleOperate" name="CircleOperate" method="POST"  action="<?php echo U('Vip/VipMyask/DoreplyOperate');?>">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">

		<tr>
			<td class="alt"><font color="red">*</font>回复内容： </td>
				<td><textarea name="content" id="content" style="width:60%;height:400px" placeholder="输入圈子介绍..."><?php echo $replyInfo['content'];?></textarea><span class="error"></span></td>
		</tr>
			
		<tr>
			<td class="alt"><font color="red">*</font>是否前台显示： </td>
			<td>
				<input type="radio" id="is_status" name="is_status" value="1" <?php if($replyInfo['status'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_status" name="is_status" value="0" <?php if($replyInfo['status'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><input type="hidden" id="id" name="id" value="<?php echo $replyInfo['id']?>">
			    <input type="submit" class="btn" onclick="return check_opercircle()" value="确认提交"></td>
		</tr>
	</table>
	</form>
	<br><br><br><br>
</div>
</div>
</body>
</html>