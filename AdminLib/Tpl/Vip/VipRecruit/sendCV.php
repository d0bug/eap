<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>职位介绍-填写简历信息</title>
	<link rel="stylesheet" type="text/css" href="/static/css/zhaopin.css">
</head>
<body>
<div class="post_banner mb20">
	<img src="/static/images/svg/banner.svg" class="banner_bg">
	<div class="post_title bgpost"><img src="/static/images/svg/infor.svg"></div>
	<div class="clean"></div>
</div>
<form id="sendForm" class="roundrect formTs" method="POST" action="">
	<input type="hidden" name="id" id="id" value="<?=$recruitmentInfo['id']?>">
	<ul class="form">
		<li><label><i class="c-red">*</i>姓名</label><input type="text" class="ftext" id="userName" name="userName" required="required" maxlength="50" placeholder="请填写真实姓名" value="<?=$recruitmentInfo['sname']?>"></li>
		<li><label><i class="c-red">*</i>手机</label><input type="text" class="ftext" id="mobile" name="mobile" required="required" pattern="1\d{10}" maxlength="11" placeholder="仅支持数字" value="<?=$recruitmentInfo['stel']?>"></li>
		<li><label><i class="c-red">*</i>性别</label>
			<select required="required" name="sex" id="sex" class="ftext arrow" style="width:69%">	
				<option value="">请选择</option>
				<option value="2" <?php if($recruitmentInfo['nsex']==2):?>selected<?php endif;?>>女</option>
				<option value="1" <?php if($recruitmentInfo['nsex']==1):?>selected<?php endif;?>>男</option></select>
			</select>
		</li>
		<li><label><i class="c-red">*</i>电子邮箱</label><input class="ftext" type="text" id="email" name="email" required="required" maxlength="300" placeholder="请填写有效的邮箱地址" value="<?=$recruitmentInfo['semail']?>"></li>
		<li><label><i class="c-red">*</i>最高学历</label>
			<select required="required" name="education" id="education" class="ftext arrow" style="width:69%">	
				<option value="">请选择</option>
				<?php if($generalList):?>
					<?php foreach ($generalList as $key=>$general):?>
						<option value="<?=$general['id']?>" <?php if($recruitmentInfo['neducation']==$general['id']):?>selected<?php endif;?>><?=$general['sname']?></option>
					<?php endforeach;?>
				<?php endif;?>
			</select>
		</li>
		<li><label><i class="c-red">*</i>毕业院校</label><input type="text" class="ftext" id="keyword" name="keyword" value="" placeholder="检索毕业院校"></li>
		<li><label>&nbsp;</label>
			<select id="school" name="school" required="required" onchange="schoolChange();" class="ftext arrow" style="width:69%">	
				<option value="">请选择</option>
				<?php if($universityList):?>
					<?php foreach ($universityList as $key=>$university):?>
						<option value="<?=$university['id']?>" <?php if($recruitmentInfo['school']==$university['id']):?>selected<?php endif;?>><?=$university['sname']?></option>
					<?php endforeach;?>
				<?php endif;?>
			</select>
		</li>
		<li><label><i class="c-red">*</i>毕业时间</label>
			<select required="required" name="year" id="year" class="selecttime arrow">	</select>
			<select required="required" name="month" id="month" class="selecttime arrow">	
				<option value="">请选择</option>
				<option value="12" <?php if($recruitmentInfo['nedumonth']==12):?>selected<?php endif;?> >12</option>
				<option value="11" <?php if($recruitmentInfo['nedumonth']==11):?>selected<?php endif;?> >11</option>
				<option value="10" <?php if($recruitmentInfo['nedumonth']==10):?>selected<?php endif;?> >10</option>
				<option value="9" <?php if($recruitmentInfo['nedumonth']==9):?>selected<?php endif;?> >9</option>
				<option value="8" <?php if($recruitmentInfo['nedumonth']==8):?>selected<?php endif;?> >8</option>
				<option value="7" <?php if($recruitmentInfo['nedumonth']==7):?>selected<?php endif;?> >7</option>
				<option value="6" <?php if($recruitmentInfo['nedumonth']==6):?>selected<?php endif;?> >6</option>
				<option value="5" <?php if($recruitmentInfo['nedumonth']==5):?>selected<?php endif;?> >5</option>
				<option value="4" <?php if($recruitmentInfo['nedumonth']==4):?>selected<?php endif;?> >4</option>
				<option value="3" <?php if($recruitmentInfo['nedumonth']==3):?>selected<?php endif;?> >3</option>
				<option value="2" <?php if($recruitmentInfo['nedumonth']==2):?>selected<?php endif;?> >2</option>
				<option value="1" <?php if($recruitmentInfo['nedumonth']==1):?>selected<?php endif;?> >1</option>
			</select>
		</li>
		<li><label><i class="c-red">*</i>专业类别</label><input type="text" class="ftext" id="zylb" id="zylb" placeholder="请填写您的专业" maxlength="200" required="required" name="major"  value="<?=$recruitmentInfo['major']?>"></li>
		<li><label><i class="c-red">*</i>应聘科目</label>
			<select required="required" name="subject" id="subject" class="ftext arrow" style="width:69%">	
				<option value="">请选择</option>
				<?php if($subjectList):?>
					<?php foreach ($subjectList as $key=>$subject):?>
					<option value="<?=$subject['sname']?>" <?php if($recruitmentInfo['skechengcode']==$subject['sname']):?>selected<?php endif;?>><?=$subject['sname']?></option>
					<?php endforeach;?>
					<option value="其他"  <?php if($recruitmentInfo['skechengcode']=='其他'):?>selected<?php endif;?>>其他</option>
				<?php endif;?>
			</select>
		</li>
		<li><label><i class="c-red">*</i>教师性质</label>
			<select required="required" name="teacherNature" id="teacherNature" class="ftext arrow" style="width:69%">	
				<option value="">请选择</option>
				<?php if($postType):?>
					<?php foreach ($postType as $key=>$type):?>
						<option value="<?=$type['id']?>" <?php if($recruitmentInfo['nposttype']===$type['id']):?>selected<?php endif;?>><?=$type['sname']?></option>
					<?php endforeach;?>
				<?php endif;?>
			</select>
		</li>
		<!--
		<select required="required" name="teachingIntention" id="teachingIntention">
			<option value="">请选择</option>
			<option value="班课教学" <?php if($recruitmentInfo['sbak']=='班课教学'):?>selected<?php endif;?>>班课教学</option>
			<option value="一对一教学" <?php if($recruitmentInfo['sbak']=='一对一教学'):?>selected<?php endif;?>>一对一教学</option>
		</select>-->
		<li><input type="button" class="btn f28 auto bold" value="提交" onclick="checkFrom()"></li>
	</ul>
</form>
</body>
<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
		jQuery(document).ready(
			function() {
				var curYear = new Date().getFullYear();
				var yearOption = '<option value="">请选择</option>';
				for (var year = curYear + 4; year >= 1954; year--) {
					yearOption += '<option value="' + year + '">' + year
							+ '</option>';
				}
			jQuery('#year').append(yearOption);
			jQuery('#year').val(<?=$recruitmentInfo['neduyear']?>);
			$('#keyword').bind('input propertychange', function() {
			    var keyword = $('#keyword').val();
			    if(keyword!=''){
			    	$.get("<?php echo U('Vip/VipRecruit/searchUniversity')?>",
						{keyword:keyword},
						function(data){
							$('#school').html(data);
						}); 
			    }
			});
		});
	
		function schoolChange() {
			var school = document.getElementById("school").value;
			if (school == "其他") {
				document.getElementById("school2").style.display = "block";
			} else {
				document.getElementById("school2").style.display = "none";
			}
		}
		function checkFrom(){
			var id = $('#id').val();
			var userName = $('#userName').val();
			var mobile = $('#mobile').val();
			var sex = $('#sex').val();
			var email = $('#email').val();
			var education = $('#education').val();
			var school = $('#school').val();
			var year = $('#year').val();
			var month = $('#month').val();
			var zylb = $('#zylb').val();
			var subject = $('#subject').val();
			var teacherNature = $('#teacherNature').val();
			if(userName==''||mobile==''||sex==''||email==''||education==''||school==''||year==''||month==''||zylb==''||subject==''||teacherNature==''){
				alert('请将所有信息填写完整');
			}else{
				if (!/^1[3|4|5|7|8][0-9]\d{8}$/.test(mobile)) {  
                	alert("您输入的手机号码不正确");  
                	return false;
           		}
           		if(!/\w+[@]{1}\w+[.]\w+/.test(email)){
           			alert("您输入的邮箱地址不正确"); 
           			return false;
           		}
				$.post(	"<?php echo U('Vip/VipRecruit/sendCV')?>",
				{id:id,userName:userName,mobile:mobile,sex:sex,email:email,education:education,school:school,year:year,month:month,major:zylb,subject:subject,teacherNature:teacherNature},
				function(data){
					if(data.status==1){
				    	alert('提交成功');
				    	window.location.href = '<?php echo U('Vip/VipRecruit/jobList')?>';
				    }else{
				    	alert('提交失败');
				    }
				}, "json");
			}
			
		}
</script>
</html>
