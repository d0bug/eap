<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>个人中心</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
</head>
<body>
<div class="myinfo">
	<div class="myphoto">
		<img src="<?php echo $user['headimgurl'];?>">
	</div>
	<p class="mycontact c-fff f14">
		姓名：<?php echo $user['user_name'];?><br />
		电话：<?php echo $user['user_mobile'];?>
	</p>
</div>
<ul class="auto mycon">
	<li><a href="<?php echo U('Vip/GsWeixin/courseSign')?>" class="coursed">我的预约</a><span>&gt;</span></li>
    <li><a href="<?php echo U('Vip/GsWeixin/hdSignList')?>" class="exerced">活动报名</a><span>&gt;</span></li>
	<li><a href="<?php echo U('Vip/GsWeixin/classOrder')?>" class="ordered">订单中心</a><span>&gt;</span></li>
	<li><a href="<?php echo U('Vip/GsWeixin/collectList')?>" class="favor">收藏夹</a><span>&gt;</span></li>
	<li><a id="J_homeworkcon-queqin" >退出登录</a><span>&gt;</span></li>
</ul>

<!--退出登录弹框-->
<div class="queqinbox hide" id="J_g_queqinbox">
	<div class="g-windowcon">
		<div class="b_wenzi f16 c-333">确认退出吗？</div>
		<div class="b_btn">
			<span class="btn_red f16 "><a href="<?php echo U('Vip/GsWeixin/logout')?>">确定</a></span>
			<span class="btn_grayxian f16 win-closebtn">取消</span>
		</div>
	</div>
</div>

<script type="text/javascript"> 

/*****缺勤弹出框***/
$("#J_homeworkcon-queqin").click(function(){
	$("#J_g_queqinbox").show();	
})


/*弹窗关闭事件*/
$(".win-closebtn").click(function(){
	$("#J_g_windowbox,.g-windowcon").hide();
	$("#J_g_queqinbox").hide();
});

</script>


<!--nav-->
<div class="home" style="bottom:8px;"><a href="index.html"></a></div>


</body>
</html>