<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>活动</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
	<?php foreach($activity as $rows){
			if(strtotime("-1 week") < strtotime($rows->bmjz)){
		?>
		<div class="course auto">
			<figure class="tour">
				<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$rows->id))?>">
					<img src="<?php echo empty($rows->thumb)?'/static/images/huodong.jpg':$rows->thumb;?>" class="w38">
				</a>
				<figcaption class="w60">
					<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$rows->id))?>">
						<h2 class="c-red f14 wellipsis pb5"><?php echo $rows->title;?></h2>
						<p><i class="c-green w38">开始时间</i><span class="w60"><?php if(strtotime($rows->ksjj) >0){ echo $rows->ksjj;}?></span></p>
						<p><i class="c-green w38">年级</i><span class="w60"><?php echo $rows->nj;?></span></p>
						<p><i class="c-green w38">报名截止日期</i><span class="w60"><?php echo $rows->bmjz;?></span></p>
					</a>
					<a href="<?php echo U('Vip/GsWeixin/activitySign', array('id'=>$rows->id))?>" class="btn_red btn_cou">我要参加</a>
				</figcaption>
				<div class="clear"></div>
			</figure>
		</div>
	<?php }}?>
<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb"><span>小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip"><span>1对1</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd_green"><span class="c-green">活动</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd"><span>预约诊断</span></a></li>
	</ul>
</div>

</body>
</html>