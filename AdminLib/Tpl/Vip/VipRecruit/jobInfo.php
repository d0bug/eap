<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>职位介绍-<?=$jobInfo['sname']?></title>
	<link rel="stylesheet" type="text/css" href="/static/css/zhaopin.css">
</head>
<body>
<div class="post_banner mb20">
	<img src="/static/images/svg/banner.svg" class="banner_bg">
	<div class="post_title bgpost"><img src="/static/images/svg/postcon.svg"></div>
	<div class="clean"></div>
</div>
<div class="post_con">
	<div class="name bold f20"><?=$jobInfo['sname']?></div>
	<span class="sanj"></span>
	<ul class="requ_list">
		<?=$jobInfo['description']?>
	</ul>
</div>
<?php if($recruitmentInfo['id']):?>
<div class="btn f28 bold" onclick="applyJob(<?=$jobInfo['id']?>,<?=$recruitmentInfo['id']?>)">投个简历</div>
<?php else:?>
<div class="btn f28 bold" onclick="javascript:window.location.href='<?php echo U('Vip/VipRecruit/sendCV')?>'">投个简历</div>
<?php endif;?>
</body>
<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
function applyJob(jobId,recruitmentId){
	if(jobId!='' && recruitmentId!=''){
		$.post(	"<?php echo U('Vip/VipRecruit/applyJob')?>",
				{jobId:jobId,recruitmentId:recruitmentId},
				function(data){
				    if(data.status==1){
				    	alert('投递成功');
				    }else{
				    	alert('投递失败');
				    }
				}, "json");
	}else{
		alert('非法操作');
	}
	
}
</script>
</html>