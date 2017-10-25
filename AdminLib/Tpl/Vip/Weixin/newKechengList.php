<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin2.css" rel="stylesheet" />
<script src="/static/js/jquery-2.1.1.min.js"></script>
<script src="/static/js/vip_wx.js"></script>
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
		<li><a href="<?php echo U('Vip/Weixin/newStudentInfo',array('student_code'=>$student_code))?>">学员信息</a><i></i></li>
		<li class="current"><a href="javascript:void(0)">学员课程</a><i></i></li>
	</ul>
	<div class="stuInfo">
	<?php if($lessonList):?>
		<?php foreach($lessonList as $key=>$lesson):?>
		<h3 class="modTit"> <?php echo $lesson['lesson_topic']?></h3>
		<div class="modCon modConTime">
			<?php echo $lesson['dtdatereal']?> <?php echo $lesson['dtlessonbeginreal']?>~<?php echo $lesson['dtlessonendreal']?> 
				<?php if(!empty($lesson['lecture_id'])):?>
					<?php if(!empty($lesson['lesson_report_url_wx'])):?>
						<a href="<?php echo U('Vip/Weixin/recordLessonTrack',array('helu_id'=>$lesson['heluid']));?>" class="blue" target="_blank">修改</a>
					<?php else:?>
						<?php if((strtotime($lesson['dtdatereal'].' '.$lesson['dtlessonendreal'])+48*3600)<time()):?>
							&nbsp;&nbsp;<font color=red>逾期未核录</font>
						<?php elseif(strtotime($lesson['dtdatereal'].' '.$lesson['dtlessonendreal']) <= time()):?>
						<a href="<?php echo U('Vip/Weixin/recordLessonTrack',array('helu_id'=>$lesson['heluid']));?>" class="blue" target="_blank">记录</a><br><br>
						<?php endif;?>
					<?php endif;?>	
				<?php else:?>
					&nbsp;&nbsp;<font color=red>待备课</font>
				<?php endif;?>
							
				<?php if(!empty($lesson['lesson_report_url_wx'])):?>
					<a href="javascript:void(0)" onclick="show_report('<?php echo $lesson['lesson_report_url_wx']?>')" class="blue" >学习报告</a>
				<?php endif;?>
		</div>
		<div class="modCon">
			<?php echo $lesson['comment']?>
		</div>
		<?php endforeach;?>
	<?php endif;?>	
	</div>
</article>
</body>
</html>