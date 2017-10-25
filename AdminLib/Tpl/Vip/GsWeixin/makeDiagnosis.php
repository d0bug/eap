<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>预约诊断</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<ul class="auto form hei86">
	<form id="diagnosedForm">
		<li class="pt10">
			<label class="w29">学生姓名<span style="color:red">*</span></label>
			<input type="text" value="" name="userName" placeholder="请输入真实姓名(必填)" class="txt">
		</li>
		<li>
			<label class="w29">家长电话<span style="color:red">*</span></label>
			<input type="text" value="" name="userMobile" placeholder="请输入手机号(必填)"  class="txt">
		</li>
		<li>
			<label class="w29">邮件</label>
			<input type="text" name="userMail" value="" placeholder="请输入邮件地址"  class="txt">
		</li>
		<li>
			<label class="w29">年级<span style="color:red">*</span></label>
			<input type="text" value="" name="userGrade" placeholder="请输入年级(必填)"  class="txt">
		</li>
        <li>
        	<label class="w29">校区<span style="color:red">*</span></label>
			<select name="userCampus" id="userCampus">
            	<option value="">请选择校区(必填)</option>
            	<?php foreach($campusList as $campus){?>
				<option value="<?php echo $campus['title'];?>"><?php echo $campus['title'];?></option>
				<?php }?>
			</select>
		</li>
		<li class="pt10"><input type="button" value="提交" class="btn_red btn_zc entered"></li>
	</form>
	<div style="text-align:center;color:red;margin-top:10%">如需查询信息，请在“<a href="<?php echo U('Vip/GsWeixin/courseSign')?>" style="color:red;text-decoration:underline;">我的预约</a>”中查看</div>	
</ul>


<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb">小班课</a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip">1对1</a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd">活动</a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd_green"><span class="c-green">预约诊断</span></a></li>
	</ul>
</div>
<script type="text/javascript">
	$('.entered').click(function(){
		var userName = $('input[name="userName"]').val();
		var userMobile = $('input[name="userMobile"]').val();
		var userGrade = $('input[name="userGrade"]').val();
		var userCampus = $('#userCampus').find("option:selected").val();
		if(!(/^1[3|4|5|7|8]\d{9}$/.test(userMobile))){ 
			alert("手机号码有误，请重填"); 
			return false; 
		}
		if(userName !== '' && userMobile !=='' && userGrade !=='' && userCampus !==''){
			$.post('<?php echo U("Vip/GsWeixin/makeDiagnosis")?>',$('#diagnosedForm').serialize(),function(data){
				if(data.ResultType == 1){
					//alert(data.Message);
					window.location.href="/Vip/GsWeixin/prompt/tel/"+userMobile+"/type/zhenduan";
				}else{
					alert(data.Message);
				}
			},'json');
		}else{
			alert('必填项不能为空');
		}
	})
</script>
</body>
</html>