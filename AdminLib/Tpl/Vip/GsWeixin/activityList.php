<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>活动详情</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<div class="course po_f">
	<figure class="tour">
		<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$details->id))?>">
			<img src="<?php echo empty($details->thumb)?'/static/images/huodong.jpg':$details->thumb;?>" class="w38">
		</a>
		<figcaption class="w60">
			<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$details->id))?>">
				<h2 class="c-red f14 wellipsis pb5"><?php echo $details->title;?></h2>
				<p><i class="c-green w38">开始时间</i><span class="w60"><?php if(strtotime($details->ksjj) >0){ echo $details->ksjj;}?></span></p>
				<p><i class="c-green w38">年级</i><span class="w60"><?php echo $details->nj;?></span></p>
				<p><i class="c-green w38">报名截止日期</i><span class="w60"><?php echo $details->bmjz;?></span></p>
			</a>
			<a href="<?php echo U('Vip/GsWeixin/activitySign', array('id'=>$details->id))?>" class="btn_red btn_cou">我要参加</a>
		</figcaption>
		<div class="clear"></div>
	</figure>
</div>

<div class="auto tab_nav ma_top140 h71">
	<ul id="pagenavi" class="page">
		<li class="w50 active">详情</li>
        <li class="w50" style="border-right:1px solid #fff;border-radius:0 3px 0 0;">猜您感兴趣的活动</li>
	</ul>
	<div id="slider" class="swipe">
		<ul class="auto tab_list">
			<li class="li_list">
				<?php echo $details->content;?>
			</li>
			<!--详情-->
			<li class="li_list hide">
				<?php 
					foreach($activity as $key=>$rows){
						if($key < 5){
				?>
				<div class="course auto">
					<figure class="tour">
						<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$rows->id))?>">
							<img src="<?php echo $rows->thumb;?>" class="w38">
						</a>
						<figcaption class="w60">
							<a href="<?php echo U('Vip/GsWeixin/activityList', array('id'=>$rows->id))?>">
								<h2 class="c-red f14 wellipsis pb5"><?php echo $rows->title;?></h2>
								<p><i class="c-green w38">开始时间</i><span class="w60"><?php echo $rows->ksjj;?></span></p>
								<p><i class="c-green w38">年级</i><span class="w60"><?php echo $rows->nj;?></span></p>
								<p><i class="c-green w38">报名截止日期</i><span class="w60"><?php echo $rows->bmjz;?></span></p>
							</a>
							<a href="<?php echo U('Vip/GsWeixin/activitySign', array('id'=>$rows->id))?>" class="btn_red btn_cou">我要参加</a>
						</figcaption>
						<div class="clear"></div>
					</figure>
				</div>
				<?php }}?>
			</li>
			<!--相关课程-->
		</ul>
	</div>
</div>

  <script>
        $(function(){
            $('#pagenavi li').click(function(){
                var index = $(this).index();
                $('#slider .li_list').eq(index).show().siblings('.li_list').hide(0);
                $(this).addClass('active').siblings().removeClass('active');
               
            });
        })
    </script>

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