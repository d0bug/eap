<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div>
	<?php if($userInfo):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt">头像：</td>
			<td><img src="<?php if($userInfo['avatar']):?><?php echo $userInfo['avatar_show'];?><?php else:?><?php echo C('default_avatar');?><?php endif;?>" width="100" height="100"></td>
		</tr>
		<tr><td class="alt">用户名：</td>
			<td><?php echo $userInfo['username']?></td>
		</tr>
		<tr><td class="alt">昵称：</td>
			<td><?php echo $userInfo['nickname']?></td>
		</tr>
		<tr><td class="alt">学员姓名：</td>
			<td><?php echo $userInfo['student_name']?></td>
		</tr>
		<tr><td class="alt">账户余额：</td>
			<td><?php echo $userInfo['account_money']?>元</td>
		</tr>
		<tr><td class="alt">邮箱：</td>
			<td><?php echo $userInfo['email']?></td>
		</tr>
		<tr><td class="alt">性别：</td>
			<td><?php if($userInfo['gender']==1):?>男<?php elseif ($userInfo['gender']==2):?>女<?php else:?>未设置<?php endif;?></td>
		</tr>
		<tr><td class="alt">年龄：</td>
			<td><?php echo $userInfo['age']?></td>
		</tr>
		<tr><td class="alt">联系电话：</td>
			<td><?php echo $userInfo['phone']?></td>
		</tr>
		<tr><td class="alt">就读年级：</td>
			<td ><?php echo $userInfo['grade']?></td>
	    </tr>
	    <tr><td class="alt">就读学校：</td>
			<td ><?php echo $userInfo['school']?></td>
	    </tr>
	    <tr><td class="alt">最新开通时间：</td>
			<td ><?php echo ($userInfo['opentime']!='0000-00-00 00:00:00' && $userInfo['opentime'] !='')?$userInfo['opentime']:''?></td>
	    </tr>
	    <tr><td class="alt">资料开通数量：</td>
			<td ><?php echo $userInfo['data_count']?></td>
	    </tr>
	    <tr><td class="alt">试卷开通数量：</td>
			<td ><?php echo $userInfo['paper_count']?></td>
	    </tr>
	    <tr><td class="alt">注册时间：</td>
			<td ><?php echo $userInfo['instime']?></td>
	    </tr>
	    <tr><td class="alt">最后登录时间：</td>
			<td ><?php echo $userInfo['lasttime']?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>