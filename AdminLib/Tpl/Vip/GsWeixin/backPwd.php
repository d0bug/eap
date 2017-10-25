<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>忘记密码</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
</head>
<body>
	<ul class="auto form">
	<form id="backForm">
		<li class="pt10">
			<label>电话<span style="color:red">*</span></label>
			<input type="text" value="" name="user_mobile" id="user_mobile" placeholder="请输入手机号"  class="txt">
			
		</li>
		<li>
			<label>&nbsp;</label>
			<span class="c-green" onclick="toCode(this)" >获取验证码</span>
		</li>
		<li>
			<label>验证码<span style="color:red">*</span></label>
			<input type="text" name="code" id="code" placeholder="请输入验证码"  class="txt">
		</li>
		<li>
			<label>新密码<span style="color:red">*</span></label>
			<input type="password" id="user_pwd" name="user_pwd" placeholder="6-8位字母或数字组成"  class="txt">
		</li>
		<li>
			<label>确认新密码<span style="color:red">*</span></label>
			<input type="password" id="make_pwd" placeholder="6-8位字母或数字组成"  class="txt">
		</li>

		<li class="pt10"><input type="button" id="modify" value="提交" class="btn_red btn_zc"></li>
	</form>	
</ul>
<script type="text/javascript">
	$('#modify').click(function(){
			var user_mobile = $('#user_mobile').val();
			var code 		= $('#code').val();
			var user_pwd 	= $('#user_pwd').val();
			var make_pwd 	= $('#make_pwd').val();
			var openId 		= "<?php echo $_GET['openId']?>";
			reg 		= /^[0-9a-zA-Z_]{6,8}$/;
			if(!(/^1[3|4|5|7|8]\d{9}$/.test(user_mobile))){ 
				alert("手机号码有误，请重填"); 
				return false; 
			}
			if(user_mobile == ''){
				alert('请输入手机号');
				return false;
			}
			if(code == ''){
				alert('请输入验证码');
				return false;
			}
			
			if(user_pwd !== make_pwd){
				alert('确认密码输入错误，重新输入');
				return false;
			}
			if(!reg.test(user_pwd)){
				alert('密码为6-8位字母或数字');
				return false;
			}
			
			$.post('<?php echo U("Vip/GsWeixin/backPwd")?>',$('#backForm').serialize(),function(data){
				
				if(data.status == 1){
					alert(data.successMsg);
					location.href = "/Vip/GsWeixin/login/openId/"+openId;
				}else{
					alert(data.errorMsg);
				}
			},"json");
	})
	function toCode(obj){
		var user_mobile = $('#user_mobile').val();
		var html = $(obj).html();
		if(html !== '获取验证码'){
			return false;
		}	
		if(user_mobile !== ''){

			$.post('<?php echo U("Vip/GsWeixin/phoneCode")?>',{action:'backCode',phone:user_mobile},function(data){
				if(data == '0'){
					alert('发送成功');
					settime(obj);
				}else if(data == '-2'){
					alert('手机号不正确');
					return false;
				}else{
					alert('发送失败,请联系客服');
					return false;
				}
					
			})
		}else{
			alert('请输入手机号');
		}
	}
	var countdown=60; 
	function settime(obj) { 
	    if (countdown == 0) { 
	        obj.innerHTML="获取验证码"; 
	        countdown = 60; 
	        return;
	    } else { 
	        obj.innerHTML="还剩(" + countdown + ")秒"; 
	        countdown--; 
	    } 
		setTimeout(function() { 
		    settime(obj) }
		    ,1000) 
	}	
	
</script>
</body>
</html>