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
<style type="text/css">
	.clearfix table tr td{
		text-align: center;
	}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="#none">教研员日报</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipJiaoyan/dailyTarget')?>">教研目标</a>
			</li>
		</ul>
	</div>
	<div id="calendar">
	<form id="documentManageForm" name="documentManageForm" method="GET" action="<?php echo U('Vip/VipJiaoyan/dailyManage')?>">
		查询时间：<input type="text"  class="Wdate" id="date" name="date" value="<?php echo $dateTime;?>" onClick="WdatePicker()"> <input type="submit" value="搜索">
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($userList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="10%">姓名</td>
				<td>授课科目</td>
				<td width="5%">日期</td>
				<td>当日新搭建讲义</td>
				<td>当日导课视频</td>
				<td>当日说课视频</td>
				<td>本月说导视频</td>
				<td>本月说导目标</td>
				<td>本月说导完成率</td>
			</tr>
			<?php foreach($userList as $key=>$handouts):?>
			<tr height=30>
				<td><?php echo $handouts['user_realname']?></td>
				<td ><?php echo $handouts['subjectAccredit'];?></td>
				<td><?php echo $handouts['dateTime'];?></td>
				<td><?php echo $handouts['lecturenum'];?></td>
				<td><?php echo $handouts['explodenum'];?></td>
				<td><?php echo $handouts['saynum'];?></td>
				<td><?php echo $handouts['num'];?></td>
				<td><?php echo $handouts['target'] ?></td>
				<td><?php echo $handouts['rate'];?></td>
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