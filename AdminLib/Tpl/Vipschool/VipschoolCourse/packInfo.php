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
	<?php if(!empty($packInfo)):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">打包名称：</td>
			<td><?php echo $packInfo['pname']?></td>
		</tr>
		<tr>
			<td class="alt">课程类型：</td>
			<td><?php echo $packInfo['ptype'] == 0?'课程已完':'课程预售' ?></td>
		</tr>
		<tr>
			<td class="alt">包含课程：</td>
			<td >
				<?php foreach($courseList as $key=>$course){?>
					<p><a style="color:blue" href="javascript:void(0);"><?php echo $course['course_name'];?></a></p>
				<?php }?>
			</td>
	    </tr>
	    <tr>
			<td class="alt">课程数量：</td>
			<td ><?php echo $packInfo['course_num'];?></td>
	    </tr>
	    <tr>
			<td class="alt">价格优惠：</td>
			<td ><?php echo $packInfo['coupon_type'] == 0?'减免:'.$packInfo['coupon_value']."元":'折扣:'.$packInfo['coupon_value']."%" ;?></td>
	    </tr>
	    <tr>
			<td class="alt">原价：</td>
			<td ><?php echo $packInfo['price'] ?>元</td>
	    </tr>
	    <tr>
			<td class="alt">优惠后价格：</td>
			<td ><?php echo $packInfo['real_price'] ?>元</td>
	    </tr>
	    <tr>
			<td class="alt">是否赠送教材：</td>
			<td ><?php echo $packInfo['is_give_book'] == 1?'是':'否' ?></td>
	    </tr>
	    <tr>
			<td class="alt">课程包介绍：</td>
			<td ><?php echo $packInfo['introduce']?></td>
	    </tr>
	    <tr>
			<td class="alt">添加时间：</td>
			<td ><?php echo $packInfo['instime']?></td>
	    </tr>
		 <tr>
			<td class="alt">最后修改时间：</td>
			<td ><?php echo $packInfo['updatetime']?$packInfo['updatetime']:"暂无修改" ?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>