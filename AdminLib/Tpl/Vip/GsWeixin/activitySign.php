<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>我的活动</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<ul class="auto form hei86">
	<form id="signForm">
		<li class="pt10"><label  class="w29">学生姓名<span style="color:red">*</span></label><input type="text" name="name" value="" placeholder="请输入真实姓名(必填)" class="txt"></li>
		<li><label class="w29">家长电话<span style="color:red">*</span></label><input type="text" name="mobile" value="" placeholder="请输入手机号(必填)"  class="txt"></li>
		<li><label class="w29">年级<span style="color:red">*</span></label><input type="text" name="year" value="" placeholder="请输入年级(必填)"  class="txt"></li>
		<li><label class="w29">报名校区<span style="color:red">*</span></label>
			<select id="campus" name="campus">
				<option value="">请选择校区(必填)</option>
				<?php foreach($campus as $campus){?>
				<option value="<?php echo $campus;?>"><?php echo $campus;?></option>
				<?php }?>
			</select>
		</li>
		<li><label class="w29">参加人数<span style="color:red">*</span></label><input type="text" name="num" value="" placeholder="请输入参加人数(必填)" class="txt"></li>
		<li><label class="w29">留言</label><textarea name="message" class="area"></textarea></li>
		<input type="hidden" value="<?php echo $hdtitle->title;?>" name="hdtitle" />
		<input type="hidden" value="<?php echo $hdtitle->id;?>" name="uid" />
		<li class="pt60"><input type="button" value="提交" class="btn_red btn_zc"></li>
	</form>	
</ul>
<script type="text/javascript">
	$(function(){
		$('.btn_red').click(function(){
			var name = $('input[name="name"]').val();
			var mobile = $('input[name="mobile"]').val();
			var year = $('input[name="year"]').val();
			var campus = $('#campus').find("option:selected").val();
			var num = $('input[name="num"]').val();
			if(!(/^1[3|4|5|7|8]\d{9}$/.test(mobile))){ 
				alert("手机号码有误，请重填"); 
				return false; 
			}
			if(name !== '' && mobile !=='' && year !=='' && campus !=='' && num !==''){
				$.post('<?php echo U("Vip/GsWeixin/activitySign")?>',$('#signForm').serialize(),function(data){
					if(data.ResultType == 1){
						//alert(data.Message);
						window.location.href="/Vip/GsWeixin/prompt/tel/"+mobile+"/title/<?php echo $hdtitle->title;?>/type/huodong";
					}else{
						alert(data.Message);
					}
				},'json');
			}else{
				alert('必填项不能为空');
			}
		})
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