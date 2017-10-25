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
</head>
<body>
<header class="header">
	<h1>系统登录</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<ul class="modTab">
		<li class="current"><a href="javascript:void(0);">高思员工登录</a><i></i></li>
		<li><a href="javascript:void(0);">兼职教师登录</a><i></i></li>
	</ul>
	<div class="loginBox">
		<div class="box" style="display:block;">
			<form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('Vip/Weixin/login',array('openId'=>$openId))?>">
                <input type="hidden" name="tabPos" value="0" />
				<dl>
					<dt>邮件地址：</dt>
					<dd class="d1">
						<label>
							<input class="span2" name="uName" id="empName" type="text" required="true" placeholder="企邮用户名">
							<span class="at">@gaosiedu.com</span>
						</label>
					</dd>
					<dt>登录密码：</dt>
					<dd class="d2">
						<input class="span4" name="uPass" type="password" required="true" placeholder="企邮密码">
					</dd>
					<dt>验证码：</dt>
					<dd class="d3">
						<label>
							<input name="captcha" class="span2" type="text" required="true" placeholder="验证码">
                            <span class="add-on">
                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
                            </span>
						</label>
					</dd>
				</dl>
				<div class="button">
					<button type="submit" class="btn">登录系统</button>
				</div>
			</form>
		</div>

		<div class="box">
			 <form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('Vip/Weixin/login',array('openId'=>$openId))?>">
                <input type="hidden" name="tabPos" value="1" />
				<dl>
					<dt>VIP社会兼职老师：</dt>
					<dd class="d2">
						<input type="hidden" id="user_type" name="user_type" value="VTeacher">
                        <input class="span2" name="uName" type="text" id="usrName" required="true" placeholder="VIP社会兼职老师用户名">
					</dd>
					<dt>登录密码：</dt>
					<dd class="d2">
						<input class="span4" name="uPass" type="password" required="true" placeholder="登录密码">
					</dd>
					<dt>验证码：</dt>
					<dd class="d3">
						<label>
							<input name="captcha" class="span2" type="text" autocomplete="off" required="true" placeholder="验证码">
                            <span class="add-on">
                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
                            </span>
						</label>
					</dd>
				</dl>
				<div class="button">
					<button type="submit" class="btn">登录系统</button>
				</div>
			</form>
		</div>
		<?php if($errors):?>
		<div class="alert alert-error">
			<ul>
			<?php foreach($errors as $errorMsg):?>
                <li><font color=red><?php echo $errorMsg?></font></li>
			<?php endforeach?>
			</ul>
		</div>
		<?php endif ?>
	</div>
</article>

<script>
$(function() {
	// 选项卡切换
	$('.modTab li').on('click', function() {
		var index = $('.modTab li').index(this);
		$(this).addClass('current').siblings().removeClass('current');
		$('.loginBox > div').eq(index).show().siblings().hide();
	});
})
</script>
</body>
</html>