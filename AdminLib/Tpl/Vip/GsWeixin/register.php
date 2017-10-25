<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>用户注册</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
</head>
<body>
<ul class="auto form">
	<form id="registerForm">
		<li class="pt10">
			<label>学生姓名<span style="color:red">*</span></label>
			<input type="text" id="user_name" name="user_name" value="" placeholder="请输入真实姓名" class="txt">
		</li>
		<li>
			<label>家长电话<span style="color:red">*</span></label>
			<input type="text" id="user_mobile" name="user_mobile" value="" placeholder="请输入手机号"  class="txt">
		</li>
		<li>
			<label>&nbsp;</label>
			<span class="c-green" onclick="toCode(this)" >获取验证码</span>
		</li>
		<li>
			<label>验证码<span style="color:red">*</span></label>
			<input type="text" id="code" name="code" value="" placeholder="请输入验证码"  class="txt">
		</li>
		<li>
			<label>用户密码<span style="color:red">*</span></label>
			<input type="password" id="user_pwd" name="user_pwd" value="" placeholder="6-8位字母或数字"  class="txt">
		</li>
		<li>
			<label>确认密码<span style="color:red">*</span></label>
			<input type="password" id="make_pwd" value="" placeholder="6-8位字母或数字"  class="txt">
		</li>
		<li><label>期望上课校区</label>
			<select name="user_dept" id="user_dept">
				<?php foreach($deptlist as $dept){?>
				<option value="<?php echo $dept['id'];?>"><?php echo $dept['title'];?></option>
				<?php }?>
			</select>
		</li>
		<input type="hidden" value="<?php echo $wechat['openid']?>" name="openid" />
		<input type="hidden" value="<?php echo $wechat['nickname']?>" name="wx_name" />
		<input type="hidden" value="<?php echo $wechat['headimgurl']?>" name="headimgurl" />
		<li class="pt10"><input type="button" value="注册" class="btn_red btn_zc toRegister"></li>
	</form>	
</ul>
<script type="text/javascript">
	$(function(){
		$('.toRegister').click(function(){
			var user_name 	= $('#user_name').val();
			var user_mobile = $('#user_mobile').val();
			var code 		= $('#code').val();
			var user_pwd 	= $('#user_pwd').val();
			var make_pwd 	= $('#make_pwd').val();
			var user_dept 	= $('#user_dept').val();
			reg 		= /^[0-9a-zA-Z_]{6,8}$/;
			
			if(user_name == ''){
				alert('请输入真实姓名');
				return false;
			}
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
			$.post('<?php echo U("Vip/GsWeixin/register")?>',$('#registerForm').serialize(),function(data){
				if(data.status == 1){
					alert(data.successMsg);
					location.href = "/Vip/GsWeixin/index";
				}else{
					alert(data.errorMsg);
				}
			},'json');
		})

	})
	function toCode(obj){
		var user_mobile = $('#user_mobile').val();
		var html = $(obj).html();
		if(html !== '获取验证码'){
			return false;
		}	
		if(user_mobile !== ''){

			$.post('<?php echo U("Vip/GsWeixin/phoneCode")?>',{phone:user_mobile},function(data){
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