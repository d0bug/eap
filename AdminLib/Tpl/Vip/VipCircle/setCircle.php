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
	<h2>圈子操作</h2>
	<form id="CircleOperate" name="CircleOperate" method="POST"  action="<?php echo U('Vip/VipCircle/DosetCircle');?>">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">

		<tr>
			<td class="alt"><font color="red">*</font>是否置顶： </td>
			<td>
				<input type="radio" id="is_top" name="is_top" value="1" <?php if($CircleInfo['is_top'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_top" name="is_top" value="0" <?php if($CircleInfo['is_top'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>是否推荐： </td>
			<td>
				<input type="radio" id="is_recommend" name="is_recommend" value="1" <?php if($CircleInfo['is_recommend'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_recommend" name="is_recommend" value="0" <?php if($CircleInfo['is_recommend'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>		
		<tr>
			<td class="alt"><font color="red">*</font>是否前台显示： </td>
			<td>
				<input type="radio" id="is_status" name="is_status" value="1" <?php if($CircleInfo['status'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_status" name="is_status" value="0" <?php if($CircleInfo['status'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><input type="hidden" id="cid" name="cid" value="<?php echo $CircleInfo['id']?>">
			    <input type="submit" class="btn" value="确认提交"></td>
		</tr>
	</table>
	</form>
	<br><br><br><br>
</div>
</div>
</body>
</html>