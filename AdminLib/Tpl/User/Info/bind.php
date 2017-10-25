<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="main">
	<h2>账号绑定：</h2>
	<form id="bind_form" name="bind_form" method="POST" action="<?php echo U('User/Info/bind')?>">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">当前登录方式：</td>
			<td ><?php echo $userRoles[$userType];?></td>
		</tr>
		<tr>
			<td class="alt">绑定账号类型：</td>
			<td >
				<select name="tobind_way" id="tobind_way">
				<?php foreach($userRoles as $key=>$role):?>
					<?php if($key != $userType):?>
					<option value="<?php echo $key;?>"><?php echo $role;?></option>
					<?php endif;?>
				<?php endforeach?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="alt">绑定账号：</td>
			<td ><input type="text" name="tobind_loginname" id="tobind_loginname" value=""></td>
		</tr>
		<tr>
			<td class="alt">绑定账号密码：</td>
			<td ><input type="password" name="tobind_password" id="tobind_password" value=""></td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td ><input type="submit" name="to_bind" value="立即绑定" class="btn" onclick="return confirm('确定要绑定此账号吗？')"></td>
		</tr>
	</table>
	</form>
</div>	
</body>
</html>