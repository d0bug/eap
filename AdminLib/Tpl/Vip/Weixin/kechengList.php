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
	<h1><?php echo $student_name?><span> / <?php echo $grade?></span></h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<ul class="modTab">
		<li><a href="<?php echo U('Vip/Weixin/studentInfo',array('student_code'=>$student_code,'kecheng_code'=>$kecheng_code,'lesson'=>$lesson))?>">学员信息</a><i></i></li>
		<li class="current"><a href="javascript:void(0)">已上课程</a><i></i></li>
	</ul>
	<div class="stuInfo heluList">
	<?php if($heluList):?>
		<?php foreach($heluList as $key=>$helu):?>
		<h3 class="modTit" style="width:100%"><?php echo $helu['lesson_date'].' '.$helu['lesson_end'];?>   <?php echo $helu['lesson_topic'];?></h3>
		<div class="modCon">
			<?php echo $helu['comment'];?>
			<?php if(($helu['comment']==''&&(strtotime($helu['lesson_date'].' '.$helu['lesson_end'])+48*3600)<time())):?>
				<div class="b c">逾期</div>
			<?php else:?>
				<?php if(strtotime($helu['lesson_date'].' '.$helu['lesson_end'])<time()): ?>
				<a href="<?php echo U('Vip/Weixin/keChengHeLu',array('act'=>'update','helu_id'=>$helu['helu_id']))?>"><div class="b">修改</div></a>
			<?php endif; ?>
			<?php endif;?>
		</div>
		
		<?php endforeach?>
	<?php endif;?>
	</div>
</article>
</body>
</html>