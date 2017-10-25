<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<?php if($userKey == 'Employee-xiecuiping'):?>
		<a href="<?php echo U('Vipschool/VipschoolUser/importUser')?>">导入历史学员</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolUser/importAccount')?>">导入学员账户金额</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolUser/importCourse')?>">导入课程</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolUser/importTeacher')?>">导入教师</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<br><br>
	<?php endif;?>
	<h2>用户管理</h2>
	<div id="search">
		<form id="searchForm" method="POST" action="">
		学员账号：<input type="text" id="username" name="username" value="<?php echo urldecode($username);?>" size="30" placeholder="请输入学员账号">&nbsp;&nbsp;&nbsp;&nbsp;
		学员姓名：<input type="text" id="student_name" name="student_name" value="<?php echo urldecode($student_name);?>" size="30" placeholder="请输入学员姓名">&nbsp;&nbsp;&nbsp;&nbsp;
		手机号码：<input type="text" id="phone" name="phone" value="<?php echo $phone?>" size="30" placeholder="请输入手机号码">&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="搜索" class="btn2">
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($userList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>ID</td>
				<td>账号</td>
				<td>学员姓名</td>
				<td>手机号</td>
				<td>账户余额</td>
				<td>注册时间</td>
				<td>最后登录时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($userList as $key=>$user):?>
			<tr height=30>
				<td><?php echo $user['id'];?></td>
				<td><?php echo $user['username'];?></td>
				<td><?php echo $user['student_name'];?></td>
				<td><?php echo $user['phone'];?></td>
				<td><?php echo $user['account_money'];?>元</td>
				<td><?php echo $user['instime'];?></td>
				<td><?php echo $user['lasttime'];?></td>
				<td>
					<a href="#" onclick="testMessageBox_userInfo(event,'<?php echo U('Vipschool/VipschoolUser/userInfo',array('uid'=>$user['id']))?>')" class="blue">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vipschool/VipschoolMarketing/orderList',array('username'=>$user['username']))?>" class="blue">订单</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>