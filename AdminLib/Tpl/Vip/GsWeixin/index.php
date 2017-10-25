<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>主页</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
</head>
<body>
<div class="auto pay">
	<img src="/static/images/xbbaoming.png">
	<div class="auto navbar">
		<div class="navbartitle">查询服务</div>
		<ul>
			<li><a href="http://www.gaosivip.com/m/index.php/Home/Index/school.html" class="b-red school_ic">查校区</a></li>
			<li><a href="http://www.gaosivip.com/m/index.php/Home/Teacher/index.html" class="b-blue teach_ic">查教师</a></li>
			<li><a href="http://www.gaosivip.com/m/index.php/Home/Kecheng/index.html" class="b-green course_ic">查课程</a></li>
			<li><a href="http://chat1423.looyu.com/chat/chat/p.do?c=33741&f=83285&n=gaosihujiao2" class="b-orange service_ic">在线客服</a></li>
		</ul>
	</div>
	
	<div class="auto navbar">
		<div class="navbartitle">选课报班</div>
		<ul>
			<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="b-green xb_ic">小班课</a></li>
			<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="b-orange vip_ic">1对1</a></li>
			<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="b-red hd_ic">活动</a></li>
			<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="b-blue zhd_ic">预约诊断</a></li>
		</ul>
	</div>

	<div class="auto navbar">
		<div class="navbartitle">我</div>
		<ul>
			<li><a href="<?php echo U('Vip/GsWeixin/courseSign');?>" class="b-red ybkc_ic">我的预约</a></li>
            <li><a href="<?php echo U('Vip/GsWeixin/hdSignList');?>" class="b-blue hdbm_ic">我的活动</a></li>
			<li><a href="<?php echo U('Vip/GsWeixin/classOrder');?>" class="b-green ddzx_ic">订单中心</a></li>
			<li><a href="<?php echo U('Vip/GsWeixin/collectList');?>" class="b-orange scj_ic">收藏夹</a></li>	
		</ul>
	</div>

</div>

</body>
</html>