<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	$("#updpasswdForm").validate({
		rules: {
			oldpasswd: {
				required: true
			},
			newpasswd: {
				required: true,
				minlength: 4,
				maxlength: 30
			},
			confirmpasswd: {
				required: true,
				minlength: 4,
				maxlength: 30,
				equalTo:"#newpasswd"
			},
		},
		messages: {
			oldpasswd: {
				required: '请输入原密码'
			},
			newpasswd: {
				required: '请输入新密码',
				minlength: '新密码长度不能小于4个字符',
				maxlength: '新密码长度不能大于30个字符'
			},
			confirmpasswd: {
				required: '请输入确认密码',
				minlength: '确认密码长度不能小于4个字符',
				maxlength: '确认密码长度不能大于30个字符',
				equalTo:  '新密码与确认密码不一致'

			}
		},
	});
})
</script>
</head>
<body>
<div id="main">
<h2>基本信息：</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">姓　　名：</td>
			<td><?php echo $userInfo['real_name'];?></td>
		</tr>
		<tr>
			<td class="alt">用 &nbsp;户&nbsp;名：</td>
			<td><?php echo $userInfo['user_name'];?></td>
		</tr>
<?php if($userInfo['mail']):?>
		<tr>
			<td class="alt">邮　　箱：</td>
			<td><?php echo $userInfo['mail'];?></td>
		</tr>
<?php endif;?>
		<tr>
			<td class="alt">所属部门：</td>
			<td><?php echo (!empty($userInfo['department']))?$userInfo['department']:'无';?>&nbsp;&nbsp;<input type="button" value="更新部门" onclick="update_department('<?php echo U('Vip/VipInfo/update_department')?>')">（<font color=red>*如果您调换了部门，可以点击“更新部门”按钮，更新数据库已保存的部门信息，方便管理员及时查看到您的最新部门信息。</font>）</td>
		</tr>
		<tr>
			<td class="alt">教师类型：</td>
			<td><?php if($is_jianzhi == 1):?>社会兼职教师<?php else:?>全职教师<?php endif;?></td>
		</tr>
		<tr>
			<td class="alt">教师身份：</td>
			<td><?php if($db_userInfo['is_teaching_and_research'] != NULL):?>
					<?php if($is_jiaoyan == 0):?>普通教师<?php else:?>教研教师<?php endif;?>
				<?php else:?>
					无
				<?php endif;?>
			</td>
		</tr>
		<tr>
			<td class="alt">科目权限：</td>
			<td><?php if($is_admin):?>全部科目(管理员无需授权)<?php else:?><?php echo $subjectNameStr;?><?php endif;?></td>
		</tr>
		<tr>
			<td class="alt">拥有角色：</td>
			<td><?php echo $userInfo['roles']?></td>
		</tr>
	</table>
<br />
<hr>
<br />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
<?php if($db_userInfo['user_mobile']):?>
		<tr>
			<td class="alt">手机号码：</td>
			<td><?php echo $db_userInfo['user_mobile'];?>&nbsp;&nbsp;<input type="button" name="modify_phone" id="" value="修改手机号码" onclick="$('#popup_common').toggle()"></td>
		</tr>
		<tr id="popup_common" style="display:none">
			<td class="alt">新手机号码：</td>
			<td><form id=modify action="<?php echo U('Vip/VipInfo/modify_mobile')?>" method="POST" onsubmit="return check_newphone()"><input type="text" name="newphone" id="newphone" value="">&nbsp;&nbsp;<input type="submit" value="提交修改"> <label class="error" id="newphone_msg"></label></form></td>
		</tr>
<?php else:?>
		<tr>
			<td class="alt">手机号码： </td>
			<td><input type="text" name="user_mobile" id="user_mobile">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="modify_phone" id="modify_phone" value="设置手机号码" onclick="set_mobile('<?php echo U('Vip/VipInfo/set_mobile')?>')"><label class="error" id="user_mobile_msg"></label></td>
		</tr>
<?php endif;?>
	</table>
<br />
<hr>
<br />
<?php if($db_userInfo['is_teacher'] == 1):?>
	<h2>修改密码</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
	<form id="updpasswdForm" method="POST" action="<?php echo U('Vip/VipInfo/updatePassword')?>">
		<tr>
			<td class="alt">旧密码：</td><td><input type="password" id="oldpasswd" name="oldpasswd" value=""></td>
		</tr>
		<tr><td class="alt">新密码：</td><td><input type="password" id="newpasswd" name="newpasswd" value=""></td></tr>
		<tr><td class="alt">确认新密码：</td><td><input type="password" id="confirmpasswd" name="confirmpasswd" value=""></td></tr>
		<tr><td class="alt">&nbsp;</td><td><input type="hidden" id="user_key" name="user_key" value="<?php echo $db_userInfo['user_key']?>"><input type="submit" value="确定修改"></td></tr>
	</form>
	</table>
<?php endif;?>

</div>
</body>
</html>