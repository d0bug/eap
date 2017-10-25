<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
 	<title>高思1对1</title>
	<link href="/static/css/vipStyle.css" rel="stylesheet" />
	<link href="/static/css/classtime.css" rel="stylesheet" />
	<link href="/static/css/vip-weixin.css" rel="stylesheet" />
	<script src="/static/js/jquery-2.1.1.min.js"></script>
	<script src="/static/js/iscroll.js"></script>
</head>
<body>
<header class="header">
	<h1>学员课时累计</h1>
	<div class="arr1"></div>
</header>
<div class="search">
<form action="<?php echo U('Vip/Weixin/myLessonHours') ?>" method="get">
	<input type="text" class="stuname" value="<?php echo $key_name; ?>" name="key_name" placeholder="学员姓名">
	<input type="submit" value="查询" class="btn-org">
</form>
</div>
<article class="wrap">
	<div class="claList">
		<div class="hd">
			<div class="th">学员姓名</div>
			<div class="th">学生状态</div>
			<div class="th">本月累计</div>
			<div class="th">总累计</div>
		</div>
		<?php if($myStudentList):?>
		<?php foreach($myStudentList as $key=>$myStudent):?>
		<div class="tr <?php if($key>=10):?>hide<?php endif;?>">
			<div class="td"><?php echo $myStudent['sstudentname'];?></div>
			<div class="td">
				<?php if($myStudent['nstudentproperty'] ==1):?>
					正常
				<?php elseif($myStudent['nstudentproperty'] ==2): ?>
					非正常
				<?php elseif($myStudent['nstudentproperty'] ==3): ?>
					已结课
				<?php else: ?>
					已退费
				<?php endif; ?>
			</div>
			<div class="td"><?php echo $myStudent['dmonthsumhours'];?></div>
			<div class="td"><?php echo $myStudent['dsumhours'];?></div>
		</div>
		<?php endforeach?>
		<?php endif;?>
		<div class="loading" style="display:none"><i></i></div><br>
		<div class="more" style="text-align: center;" onclick="loadingData()">点击查看更多</div>
	</div>
</article>
<script>
function loadingData(){
	$("div .hide").each(function(index){
		if(index<10){
			$(this).slideDown();
			$(this).removeClass('hide');
		}
	});
	if($('div .hide').length==0){
		$('.more').hide();
	}
}
</script>
</body>
</html>