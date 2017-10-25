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
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>预约用户管理</h2>
	<div id="search">
		<form method="POST" action="">
			学员姓名：<input type="text" id="name" name="name" value="<?php echo $name;?>"  placeholder="输入用户名">&nbsp;&nbsp;&nbsp;手机号码：<input type="text" name="mobile" id="mobile" value="<?php echo $mobile;?>">&nbsp;&nbsp;
			<input type="submit" value="搜索" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo U('Eval/EvalUser/bookexportExcel',array('type'=>0,'name'=>$name,'mobile'=>$mobile))?>" class="blue">导出Excel</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($bookuserList):?>
		<table width="70%" border="1">
			<tr bgcolor="#dddddd" height=35>
				<th>序号</th>
				<th>学生姓名</th>
				<th>手机号</th>
				<th>年级</th>
				<th>邮件</th>
				<th>预约时间</th>
			</tr>
			<?php foreach($bookuserList as $key=>$user):?>
			<tr height=30>
				<td align="center"><?php echo $user['id']?></td>
				<td><?php echo $user['name'];?></td>
				<td><?php echo $user['phone'];?></td>
				<td><?php echo $user['grade'];?></td>
				<td><?php echo $user['email'];?></td>
				<td><?php echo $user['instime'];?></td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>