
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>预约成功</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<div class=" auto warp">
	<img src="/static/images/yuyue.png">
	<?php if($find['type'] == 'kecheng'){?>
	<div class="auto yy_con">
		<p>您好，恭喜您成功预约“<span style="color:red"><?php echo $find['title'];?></span>”课程！您的电话是<span style="color:red"><?php echo $find['tel'];?></span>，我们的工作人员将在2个工作日内和您取得联系！</p>
		<p>如有问题，请您直接联系010-56639988</p>

	</div>
	<a href="javascript:;" onclick="history.go(-2)" class="btn_red btn_zc">返回</a>
	<?php }else if($find['type'] == 'huodong'){?>
		<div class="auto yy_con">
			<p>您好，恭喜您成功预约“<span style="color:red"><?php echo $find['title'];?></span>”！您的电话是<span style="color:red"><?php echo $find['tel'];?></span>，我们的工作人员将在活动的前一天与您取得联系！</p>
			<p>如有问题，请您直接联系010-56639988</p>

		</div>
		<a href="javascript:;" onclick="history.go(-2)" class="btn_red btn_zc">返回</a>
	<?php }else{ ?>
		<div class="auto yy_con">
			<p>您好，恭喜您成功预约！您的电话是<span style="color:red"><?php echo $find['tel'];?></span>，我们的工作人员将在2个工作日内和您取得联系！祝您诊断愉快！</p>
			<p>如有问题，请您直接联系010-56639988</p>
		</div>
		<a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="btn_red btn_zc">返回</a>
	<?php }?>
	
</div>

<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb"><span>小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip"><span>1对1</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd"><span>活动</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd_green"><span class="c-green">预约诊断</span></a></li>
	</ul>
</div>
</body>
</html>