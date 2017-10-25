<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>登录</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{background-color: #fff;}
	</style>
</head>
<body>
<div class="login"><img src="/static/images/loginbanner.png"></div>
<ul class="auto loginform">
	<form id="loginForm">
		<li><input type="text" value="" id="user_mobile" name="user_mobile" placeholder="请输入手机号"  class="file"></li>
		<li><input type="password" value="" id="user_pwd" name="user_pwd" placeholder="请输入密码"  class="file click"><a href="<?php echo U("Vip/GsWeixin/backPwd",array('openId'=>$openid))?>" class="pass">忘记密码？</a></li>
		<li class="pt10"><input type="button" id="entry" value="登录" class="btn_red btn_lo"></li>
		<li><a href="<?php echo U("Vip/GsWeixin/register",array('openId'=>$openid))?>" class="btn_grayxian btn_lo">新同学注册</a></li>
		<input type="hidden" value="<?php echo $openid;?>" name ="openid"/>
		<input type="hidden" value="login" name="action" />
	</form>	
</ul>
<script type="text/javascript">
	$('#entry').click(function(){
		var user_mobile = $('#user_mobile').val();
		var user_pwd = $('#user_pwd').val();
		
		if(user_mobile =='' || user_pwd ==''){
			alert('手机号和密码不能为空');
			return false;
		}else{
			
			$.post('<?php echo U("Vip/GsWeixin/login")?>',$('#loginForm').serialize(),function(data){
				if(data == true){
					alert('登录成功');
					location.href = "/Vip/GsWeixin/index";
				}else{
					alert('用户名和密码错误~');
				}
			})
		}	
	})
</script>
</body>
</html>