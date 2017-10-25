<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>职位介绍-预览简历</title>
	<link rel="stylesheet" type="text/css" href="/static/css/zhaopin.css">
</head>
<body>
<div class="post_banner mb20">
	<img src="/static/images/svg/banner.svg" class="banner_bg">
	<div class="post_title bgpost"><img src="/static/images/svg/seeinfor.svg"></div>
	<div class="clean"></div>
</div>

<ul class="infor_list">
	<li><label>姓名：</label><span><?=$recruitmentInfo['sname']?></span></li>
	<li><label>手机：</label><span><?=$recruitmentInfo['stel']?></span></li>
	<li><label>性别：</label><span><?php if($recruitmentInfo['nsex']==2):?>女<?php else:?>男<?php endif;?></span></li>
	<li><label>电子邮箱：</label><span><?=$recruitmentInfo['semail']?></span></li>
	<li><label>最高学历：</label><span><?=$recruitmentInfo['educationName']?></span></li>
	<li><label>毕业学校：</label><span><?=$recruitmentInfo['schoolName']?></span></li>
	<li><label>毕业时间：</label><span><?php if($recruitmentInfo['neduyear']):?><?=$recruitmentInfo['neduyear']?>年<?=$recruitmentInfo['nedumonth']?>月<?php endif;?></span></li>
	<li><label>专业类别：</label><span><?=$recruitmentInfo['major']?></span></li>
	<li><label>应聘项目：</label><span><?=$recruitmentInfo['skechengcode']?></span></li>
	<li><label>教师性质：</label><span><?=$recruitmentInfo['postTypeName']?></span></li>
		
	<li><a class="btn f28 auto bold" href="<?=U('Vip/VipRecruit/sendCV')?>">编辑</a></li>
</ul>

</body>
</html>