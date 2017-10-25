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
	<h2>快递管理</h2>
	<div id="search">
		<form id="searchForm" method="POST" action="">
		付款开始时间：<input type="text" id="starttime" name="starttime" value="<?php echo $starttime?>" class="Wdate" onClick="WdatePicker()">
		截止时间：<input type="text" id="endtime" name="endtime" value="<?php echo $endtime?>" class="Wdate" onClick="WdatePicker({minDate:$('#starttime').val()})">&nbsp;&nbsp;&nbsp;&nbsp;
		付款情况：<select id="type" name="type">
					<option value="">全部</option>
					<option value="1" <?php if($type==1):?>selected<?php endif;?>>已付款</option>
					<option value="2" <?php if($type==2):?>selected<?php endif;?>>未付款</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="搜索" class="btn2">&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolMarketing/export_expressList',array('starttime'=>$starttime,'endtime'=>$endtime,'type'=>$type))?>" class="blue">导出Excel表</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($expressList):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;ID</td>
				<td>订单编号</td>
				<td>付款时间</td>
				<td>购买者</td>
				<td>教材</td>
				<td>收件人</td>
				<td>收件地址</td>
				<td>邮编</td>
				<td>联系方式</td>
				<td>发件状态</td>
				<td>操作</td>
			</tr>
			<?php foreach($expressList as $key=>$express):?>
			<tr height=30>
				<td>&nbsp; <?php echo $express['eid'];?></td>
				<td><?php echo $express['order_number'];?></td>
				<td><?php echo $express['paytime'];?></td>
				<td><?php echo $express['username'];?></td>
				<td><?php echo $express['textbook'];?></td>
				<td><?php echo $express['real_name'];?></td>
				<td><?php echo $express['address'];?></td>
				<td><?php echo $express['postcode'];?></td>
				<td><?php echo $express['phone'];?></td>
				<td><?php echo ($express['status']==0)?'<font color=red>未发件</font>':'<font color=green>已发件</font>';?></td>
				<td><a href="#" class="blue" onclick="testMessageBox_viewExpress(event,'<?php echo U('Vipschool/VipschoolMarketing/expressInfo',array('eid'=>$express['eid']))?>')">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if($express['status']==0 && !empty($express['paytime'])):?>
					<a href="#" onclick="testMessageBox_sendExpress(event,'<?php echo U('Vipschool/VipschoolMarketing/sendExpress',array('eid'=>$express['eid']))?>')" class="blue">发件</a>
				<?php endif;?>
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