<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>校园招聘</title>
	<link rel="stylesheet" type="text/css" href="/static/css/zhaopin.css">
</head>
<body>	
<div class="banner mb20"><img src="/static/images/svg/indexbanner.svg"></div>
<div>
<?php if($recruitmentInfo):?>
	<a href="<?php echo U('Vip/VipRecruit/myCV')?>" class="btn f28 bold " >我的简历</a></div>
<?php else:?>
	<a href="<?php echo U('Vip/VipRecruit/sendCV')?>" class="btn f28 bold " >填写简历</a></div>
<?php endif;?>
<div class="post">
	<div class="post_name f20 bold c-blue">招聘职位</div>
	<ul class="post_list">
	<?php if($jobList):?>
		<?php foreach ($jobList as $key=>$job):?>
		<li><span class="cirle"></span><a href="<?=U('Vip/VipRecruit/jobInfo',array('id'=>$job['id']))?>"><?=$job['sname']?></a></li>
		<?php endforeach;?>
	<?php else:?>
	暂无职位！
	<?php endif;?>
	</ul>
</div>
</body>
</html>
