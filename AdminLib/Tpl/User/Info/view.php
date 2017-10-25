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
	<h2>个人信息：</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">姓　名：</td>
			<td><?php echo $userInfo['real_name'];?></td>
		</tr>
		<tr>
			<td class="alt">用户名：</td>
			<td><?php echo $userInfo['user_name'];?></td>
		</tr>
		<tr>
			<td class="alt">邮　箱：</td>
			<td><?php echo $userInfo['mail'];?></td>
		</tr>
<?php if($userInfo['user_type'] == 'VIP系统用户'):?>
		<tr>
			<td class="alt">教师编码：</td>
			<td><?php echo $userInfo['teacher_info']['scode'];?></td>
		</tr>
		<tr>
			<td class="alt">教师类型：</td>
			<td>普通教师</td>
		</tr>
		<tr>
			<td class="alt">授课风格：</td>
			<td><?php echo $userInfo['teacher_info']['sshoukefengge'];?></td>
		</tr>
<?php endif;?>
<?php if($userInfo['user_type'] == '内部员工'):?>
		<tr>
			<td class="alt">所属部门：</td>
			<td><?php if($userInfo['department']):?><?php echo $userInfo['department'];?><?php else:?>无<?php endif;?></td>
		</tr>
<?php endif;?>
<?php if($userInfo['user_type'] == '思泉语文(教师)' ||$userInfo['user_type'] == '思泉语文(助教)'):?>
		<tr>
			<td class="alt">教师编码：</td>
			<td><?php echo $userInfo['scode'];?></td>
		</tr>
		<tr>
			<td class="alt">教师类型：</td>
			<td><?php if($userInfo['user_type'] == '思泉语文(助教)'):?>助教<?php else:?>教师<?php endif;?></td>
		</tr>
		<tr>
			<td class="alt">联系电话：</td>
			<td><?php echo $userInfo['sphone'];?></td>
		</tr>
<?php endif;?>
		<tr>
			<td class="alt">当前登录方式：</td>
			<td><?php echo $userInfo['user_type'];?></td>
		</tr>
	</table>
	
	
	<h2>绑定账号信息：</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
	<?php foreach($myRelations as $key=>$relation):?>
		<tr>
			<td class="alt"><?php echo $TypeArray[$relation['rel_user_type']];?>:</td>
			<td><?php echo $relation['rel_user_name'];?>&nbsp;&nbsp;<a href="<?php echo U('User/Info/release_bind',array('user_key'=>$userKey,'rel_user_key'=>$relation['rel_user_key']))?>" style="color:blue;"  onclick="return confirm('确定要将此账号解除绑定吗？')">解绑该账号</a></td>
		</tr>
	<?php endforeach?>
	</table>
</div>	
</body>
</html>