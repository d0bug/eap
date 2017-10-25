<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>一对一预约</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<ul class="auto form hei86">
	<form id="makeForm">
		<li class="pt10"><label class="w29">学生姓名<span style="color:red">*</span></label><input type="text" name="name" value="" placeholder="请输入真实姓名(必填)" class="txt"></li>
		<li><label class="w29">家长电话<span style="color:red">*</span></label><input type="text" name="tel" value="" placeholder="请输入手机号(必填)"  class="txt"></li>
		<li><label class="w29">邮箱</label><input type="text" name="email" value="" placeholder="请输入邮箱(非必填)"  class="txt"></li>
		<li><label class="w29">年级<span style="color:red">*</span></label><input type="text" name="nianji" value="" placeholder="请输入年级(必填)"  class="txt"></li>
		<li class="pt10"><input type="button" value="提交" class="btn_red btn_zc"></li>
		<input type="hidden" value="<?php echo $kctitle;?>" name="kctitle" />
	</form>
</ul>

<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb"><span>小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip_green"><span class="c-green">1对1</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd"><span>活动</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd"><span>预约诊断</span></a></li>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		$('.btn_red').click(function(){
			var name = $('input[name="name"]').val();
			var tel = $('input[name="tel"]').val();
			var nianji = $('input[name="nianji"]').val();
			if(!(/^1[3|4|5|7|8]\d{9}$/.test(tel))){ 
				alert("手机号码有误，请重填"); 
				return false; 
			}
			if( name !== '' && tel !== '' && nianji !==''){
				$.post('<?php echo U("Vip/GsWeixin/courseMake")?>',$('#makeForm').serialize(),function(data){
					if(data.ResultType == 1){
						//alert(data.Message);
						window.location.href="/Vip/GsWeixin/prompt/tel/"+tel+"/title/<?php echo $kctitle;?>/type/kecheng";
					}else{
						alert(data.Message);
						return false;
					}
				},'json')	
			}else{
				alert('必填的不能为空');
				return false;
			}
		})
	})
</script>
</body>
</html>