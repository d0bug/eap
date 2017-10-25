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
	<h2>订单管理</h2>
	<div id="search">
		<form id="searchForm" method="POST" action="<?php echo U('Vipschool/VipschoolMarketing/orderList')?>">
		订单编号：<input type="text" id="order_number" name="order_number" value="<?php echo urldecode($order_number);?>" size="25" placeholder="请输入订单编号">&nbsp;&nbsp;&nbsp;&nbsp;
		下单开始时间：<input type="text" id="starttime" name="starttime" value="<?php echo $starttime?>" class="Wdate" onClick="WdatePicker()">
		截止时间：<input type="text" id="endtime" name="endtime" value="<?php echo $endtime?>" class="Wdate" onClick="WdatePicker({minDate:$('#starttime').val()})">&nbsp;&nbsp;&nbsp;&nbsp;
		订单状态：<select id="type" name="type">
					<option value="">全部</option>
					<option value="1" <?php if($type==1):?>selected<?php endif;?>>已付款</option>
					<option value="2" <?php if($type==2):?>selected<?php endif;?>>未付款</option>
					<option value="3" <?php if($type==3):?>selected<?php endif;?>>已退款</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		学员账号：<input type="text" id="username" name="username" value="<?php echo $username?>">&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="搜索" class="btn2">&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolMarketing/export_orderList',array('starttime'=>$starttime,'endtime'=>$endtime,'type'=>$type,'username'=>$username))?>" class="blue">导出Excel表</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($orderList):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;ID</td>
				<td>订单编号</td>
				<td>下单时间</td>
				<td>总计金额（元）</td>
				<td>支付方式</td>
				<td>订单内容</td>
				<td>购买者</td>
				<td>订单状态</td>
				<td>操作</td>
			</tr>
			<?php foreach($orderList as $key=>$order):?>
			<tr height=30>
				<td>&nbsp; <?php echo $order['oid'];?></td>
				<td><?php echo $order['order_number'];?></td>
				<td><?php echo $order['instime'];?></td>
				<td><?php echo $order['real_amount'];?></td>
				<td>
					<?php if($order['is_studycard']==1):?>
						<?php if($order['real_amount'] == 0):?>
							学习卡
						<?php else:?>
							学习卡+在线付款
						<?php endif;?>
					<?php else:?>
						在线付款
					<?php endif;?>
				</td>
				<td><?php echo $order['order_content'];?></td>
				<td><?php echo $order['username'];?></td>
				<td><?php if($order['status']==1):?><font color=green>已付款</font><?php elseif($order['status']==2):?><font color=gray>已退款</font><?php else:?><font color=red>未付款</font><?php endif;?></td>
				<td>
					<a href="#" class="blue" onclick="testMessageBox_viewOrder(event,'<?php echo U('Vipschool/VipschoolMarketing/orderInfo',array('oid'=>$order['oid']))?>')">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
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