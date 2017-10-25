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
	<?php if($expressInfo):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt">快递ID：</td>
			<td><?php echo $expressInfo['eid']?></td>
		</tr>
		<tr><td class="alt">订单编号：</td>
			<td><?php echo $expressInfo['order_number']?></td>
		</tr>
		<tr><td class="alt">付款时间：</td>
			<td><?php echo $expressInfo['paytime']?></td>
		</tr>
		<tr><td class="alt">购买者：</td>
			<td><?php echo $expressInfo['username']?></td>
		</tr>
		<tr><td class="alt">教材：</td>
			<td ><?php echo $expressInfo['textbook']?></td>
	    </tr>
	    <tr><td class="alt">收件人：</td>
			<td ><?php echo $expressInfo['real_name']?></td>
	    </tr>
	    <!--<tr><td class="alt">省市：</td>
			<td ><?php echo $expressInfo['province']?>-<?php echo $expressInfo['city']?></td>
	    </tr>-->
	    <tr><td class="alt">收货地址：</td>
			<td ><?php echo $expressInfo['address']?></td>
	    </tr>
	    <tr><td class="alt">邮编：</td>
			<td ><?php echo $expressInfo['postcode']?></td>
	    </tr>
	    <tr><td class="alt">联系方式：</td>
			<td ><?php echo $expressInfo['phone']?></td>
	    </tr>
	    <tr><td class="alt">快递状态：</td>
			<td ><?php if($expressInfo['status']==1):?><font color=green>已发件</font><?php elseif($expressInfo['status']==2):?>已退件<?php else:?><font color=red>未发件</font><?php endif;?></td>
	    </tr>
	     <tr><td class="alt">快递公司：</td>
			<td ><?php echo $expressInfo['express_company']?></td>
	    </tr>
	     <tr><td class="alt">快递单号：</td>
			<td ><?php echo $expressInfo['express_number']?></td>
	    </tr>
	     <tr><td class="alt">发件时间：</td>
			<td ><?php echo $expressInfo['sendtime']?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>