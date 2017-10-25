<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="/static/js/vip.js"></script>

<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
  <link href="/static/Ueditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">

        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap-theme.min.css">
         <link href="/static/Ueditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">

        <script type="text/javascript" src="/static/Ueditor/third-party/jquery.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="/static/Ueditor/umeditor.config.js"></script>
        <script type="text/javascript" charset="utf-8" src="/static/Ueditor/umeditor.min.js"></script>
        <script type="text/javascript" src="/static/Ueditor/lang/zh-cn/zh-cn.js"></script>

</head>
<body >
<div region="center" >
<div id="main">
	<h2>添加班级</h2>
	<form id="form"  method="POST" enctype="multipart/form-data" action="<?php echo $action;?>">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">

	<tr>
		<td class="alt"><font color="red">*</font>班级：</td>
		<td>
		<input type="hidden" name="nId" value="<?php echo $nId;?>">
		<input type="checkbox" onclick="selectall()">全选
		<?php foreach($classList as $value):?>
			<input type="checkbox" class="sClassCode" name="sClassCode[]" value="<?php echo trim($value['sclasscode']);?>"  <?php if(in_array(trim($value['sclasscode']), $sClassCode)) echo 'checked';?>><?php echo $value['sclassname'];?>

		<?php endforeach?>

		</td>

	</tr>

		<tr>
			<td class="alt"><font color="red">*</font>标题： </td>
			<td>

				<input type="text" name="sTitle" id="sTitle" value="<?php echo $sTitle;?>" size="32">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>内容： </td>
			<td>


				<textarea name="sContent" id="sContent" cols="160" rows="3"><?php echo $sContent;?></textarea>
			</td>
		</tr>


		<tr>



			<td class="alt"><font color="red">*</font>启用： </td>
			<td>
				<input type="radio" id="status1" name="status" value="1" checked>是&nbsp;&nbsp;
				<input type="radio" id="status0" name="status" value="0" >否
			</td>
		</tr>

		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			   <button type="submit" class="btn">确认提交</button></td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
	<div style="color:red">注意事项：</div>
		1. 注意事项<br>
		2. 注意事项；<br>

	</div>
	<br><br><br><br>
</div>
</div>
<script type="text/javascript">
 UM.getEditor('sContent');


	var i = 1;
	function selectall() {
		if(i == 1) {
			$(".sClassCode").attr("checked",true);
		}
		if(i == -1) {
			$(".sClassCode").attr("checked",false);
		}
		i = -1*i;

	}
</script>
</body>
</html>
