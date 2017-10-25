<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center" >
	<div id="main">
		<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipVideo/uploadOss')?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt" valign="top"><font color="red">*</font>选择上传视频： </td>
				<td valign="top">
					<input type="file" id="file" name="file" value="">
				</td>
			</tr>
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
				   <input type="submit" name="submit" value="Submit" />
				</td>
			</tr>
		</table>
		</form>
	</div>
</div>
</body>
</html>