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
<header class="header">
	<h1>活动</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<ul class="modTab">
		<li class="current"><a href="<?php echo U('Vip/Weixin/newList')?>">最新活动</a><i></i></li>
		<li ><a href="<?php echo U('Vip/Weixin/hotList')?>">热点推荐</a><i></i></li>
	</ul>
	<?php if(!empty($newList)):?>
	<ul class="newsList" id="scroller">
		<?php foreach($newList as $key=>$news):?>
		<li><a href="<?php echo $news['url']?>" target="_blank">
			<div class="tit"><?php echo $news['title']?></div>
			<div class="time"><?php echo $news['inputtime']?></div>
		</a></li>
		<?php endforeach?>
	</ul>
	<?php endif;?>
</article>
</body>
</html>