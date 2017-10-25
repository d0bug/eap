<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin.css" rel="stylesheet" />
<script src="/static/js/jquery-2.1.1.min.js"></script>
<script src="/static/js/iscroll.js"></script>
</head>
<body>
<header class="header faceHeader">
	<div class="face"><img src="/static/images/face.jpg" /></div>
	<h1><?php echo $studentInfo['sstudentname']?><span> / <?php echo $studentInfo['gradename']?></span></h1>
	<div class="arr"></div>
</header>

<article class="wrap">
	<ul class="modTab">
		<li class="current"><a href="javascript:void(0)">学员信息</a><i></i></li>
		<li><a href="<?php echo U('Vip/Weixin/kechengList',array('student_code'=>$student_code,'kecheng_code'=>$kecheng_code,'student_name'=>$studentInfo['sstudentname'],'grade'=>$studentInfo['gradename'],'lesson'=>$lesson))?>">已上课程</a><i></i></li>
	</ul>
	<div class="stuInfo">
		<h3 class="modTit">学员情况登记：</h3>
		<div class="modCon">
			<dl class="modInfo">
				<dt>登记校区：</dt>
				<dd><?php echo $studentInfo['sdeptname'];?></dd>
				<dt>签约日期：</dt>
				<dd><?php echo $studentInfo['dtdate']?></dd>
				<dt>咨询师：</dt>
				<dd><?php echo $studentInfo['soperatorname'];?> </dd>
			</dl>
		</div>
		<h3 class="modTit">学员基本信息：</h3>
		<div class="modCon">
			<dl class="modInfo">
				<dt>学员姓名：</dt>
				<dd><?php echo $studentInfo['sstudentname'];?></dd>
				<dt>学员性别：</dt>
				<dd><?php echo ($studentInfo['ngender']==1)?'男':'女';?></dd>
				<dt>所在年级：</dt>
				<dd><?php echo $studentInfo['gradename'];?></dd>
				<dt>所在学校：</dt>
				<dd><?php echo $studentInfo['sschool'];?></dd>
				<dt>出生日期：</dt>
				<dd><?php echo $studentInfo['dtbirthday'];?></dd>
				<dt>兴趣爱好：</dt>
				<dd><?php echo $studentInfo['shobby'];?></dd>
				<dt>学员性格：</dt>
				<dd><?php echo $studentInfo['scharacter'];?></dd>
				<dt>班级排名：</dt>
				<dd><?php echo $studentInfo['nrank'];?></dd>
				<dt>空闲时间：</dt>
				<dd><?php echo $studentInfo['sfeetime'];?></dd>
				<dt>家长/电话1：</dt>
				<dd><?php echo $studentInfo['sparents1name']?> <?php if($studentInfo['nparents1relation']):?>(<?php echo $studentInfo['nparents1relation']?>)<?php endif;?>  
					<a href="tel:<?php echo $studentInfo['sparents1phone'];?>"><?php echo $studentInfo['sparents1phone'];?></a></dd>
				<dt>家长/电话2：</dt>
				<dd><?php echo $studentInfo['sparents2name']?> <?php if($studentInfo['nparents2relation']):?>(<?php echo $studentInfo['nparents2relation']?>)<?php endif;?>  
					<a href="tel:<?php echo $studentInfo['sparents2phone'];?>"><?php echo $studentInfo['sparents2phone'];?></a></dd>
				<dt>家长邮箱：</dt>
				<dd><a href="mailto:<?php echo $studentInfo['semail'];?>" ><?php echo $studentInfo['semail'];?></a></dd>
			</dl>
		</div>
	</div>
</article>
</body>
</html>