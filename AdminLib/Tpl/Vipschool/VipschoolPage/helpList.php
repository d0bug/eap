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
	<h2>帮助管理</h2>
	<hr>
	<div id="list" class="clearfix">
		<?php if($helpList):?>
			<table width="100%" class="tableInfo">
				<tr bgcolor="#dddddd" height=35>
					<td>ID</td>
					<td>标题</td>
					<td>类型</td>
					<td>添加时间</td>
					<td>操作</td>
				</tr>
				<?php foreach($helpList as $key=>$help):?>
				<tr height=30>
					<td><?php echo $help['hid'];?></td>
					<td><?php echo $help['title'];?></td>
					<td><?php echo $help['type_name'];?></td>
					<td><?php echo $help['instime'];?></td>
					<td>
						<a href="<?php echo U('Vipschool/VipschoolPage/updateHelp',array('hid'=>$help['hid']))?>" class="blue" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<?php endforeach?>
			</table>
		<?php else:?>
			<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>