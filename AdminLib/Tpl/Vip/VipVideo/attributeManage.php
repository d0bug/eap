<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center" >
	<div id="main">
		<div id="setAttribute" style="display:<?php if($permInfo['permValue']==3):?>block<?php else:?>none<?php endif;?>">
			<h2>视频属性管理</h2>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>视频属性：</td>
						<td>
							<div id="attribute_one_div" class="selBar">
							<?php foreach($attributeOneArr as $key=>$attributeOne):?>
								<input type="radio" name="attribute_one" id="attribute_one<?php echo $attributeOne['aid'];?>" value="<?php echo $attributeOne['aid'];?>" title="<?php echo $attributeOne['name'];?>" onclick="select_attributeTwo(this.value,'#attribute_two_div','<?php echo U('Vip/VipVideo/getAttributeTwo')?>')"><?php echo $attributeOne['name'];?>&nbsp;&nbsp;
							<?php endforeach?>
							</div>
							<div class="btnBar">
							<?php if($permInfo['permValue']==3):?>
								<a href="#" onclick="testMessageBox_addAttribute(event,'attribute_one','<?php echo U('Vip/VipVideo/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return deleteAttribute('attribute_one','<?php echo U('Vip/VipVideo/deleteAttribute')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_editAttribute(event,'attribute_one','<?php echo U('Vip/VipVideo/editAttribute')?>')"><img src="/static/images/edit.png"></a>
							<?php endif;?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>视频类别：</td>
						<td>
							<div id="attribute_two_div" class="selBar">
								<font color="red">请先选择视频属性！</font>
							</div>
							<div class="btnBar">
							<?php if($permInfo['permValue']==3):?>
								<a href="#" onclick="testMessageBox_addAttribute(event,'attribute_two','<?php echo U('Vip/VipVideo/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return deleteAttribute('attribute_two','<?php echo U('Vip/VipVideo/deleteAttribute')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_editAttribute(event,'attribute_two','<?php echo U('Vip/VipVideo/editAttribute')?>')"><img src="/static/images/edit.png"></a>
							<?php endif;?>
							</div>
						</td>
					</tr>
			</table>
		</div>
	</div>
</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
</html>