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
	<?php if($orderInfo):?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr><td class="alt">订单编号：</td>
			<td><?php echo $orderInfo['order_number']?></td>
		</tr>
		<tr><td class="alt">下单时间：</td>
			<td><?php echo $orderInfo['instime']?></td>
		</tr>
		<tr><td class="alt">付款时间：</td>
			<td><?php echo $orderInfo['paytime']?></td>
		</tr>
		<tr><td class="alt">订单金额：</td>
			<td ><?php echo $orderInfo['real_amount']?>元</td>
	    </tr>
		<tr><td class="alt">支付方式：</td>
			<td ><?php if($orderInfo['real_amount']==$orderInfo['total_amount']):?>在线付款<?php elseif($orderInfo['real_amount']==0):?>学习卡<?php else:?>学习卡+在线付款<?php endif;?></td>
	    </tr>
	    <?php if(!empty($orderInfo['studycard_code'])):?>
	    <tr><td class="alt">使用学习卡卡号：</td>
			<td ><?php echo $orderInfo['studycard_code']?></td>
	    </tr>
	    <?php endif;?>
	    <tr><td class="alt">订单内容：</td>
			<td ><?php echo $orderInfo['order_content']?></td>
	    </tr>
	     <tr><td class="alt" valign="top">包含课程：</td>
			 <td>
				<table class="tableInfo" width="90%">
				<?php if(!empty($orderInfo['pack_arr'])):?>
					<tr><td>打包课程</td><td></td></tr>
					<?php foreach ($orderInfo['pack_arr'] as $key=>$pack):?>
					<tr><td><a href="javascript:void(0);" style="color:blue"><?php echo $pack['pname'];?></a></td><td></td></tr>
					<?php endforeach;?>
				<?php endif;?>
				<?php if(!empty($orderInfo['course_arr'])):?>
					<tr><td>单品课程</td><td></td></tr>
					<?php foreach ($orderInfo['course_arr'] as $key=>$course):?>
					<tr><td><a href="javascript:void(0);" style="color:blue"><?php echo $course['course_name'];?></a></td><td></td></tr>
					<?php endforeach;?>
				<?php endif;?>
				</table>
			 </td>
	    </tr>
	    <tr><td class="alt">购买者：</td>
			<td ><?php echo $orderInfo['username']?></td>
	    </tr>
	    <tr><td class="alt">订单状态：</td>
			<td ><?php if($orderInfo['status']==1):?><font color=green>已付款</font><?php elseif($orderInfo['status']==2):?><font color=gray>已退款</font><?php else:?><font color=red>未付款</font><?php endif;?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>