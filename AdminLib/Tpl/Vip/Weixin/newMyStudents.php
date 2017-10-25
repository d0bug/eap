<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin2.css" rel="stylesheet" />
<script src="/static/js/jquery-2.1.1.min.js"></script>
<script src="/static/js/iscroll.js"></script>
</head>
<body>
<header class="header">
	<h1>我的学员</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<div class="stuList">
		<div class="hd">
		<?php if($order=='asc'):?>
			<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'sStudentName','order'=>'desc'));?>">学员姓名<?php if($key_name=='sStudentName'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='sStudentName'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
				<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'nGrade','order'=>'desc'));?>">年级<?php if($key_name=='nGrade'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='nGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
				<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'sClassAdviserCode','order'=>'desc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
			<?php else:?>
			<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'sStudentName','order'=>'asc'));?>">学员姓名<?php if($key_name=='sStudentName'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='sStudentName'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
				<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'nGrade','order'=>'asc'));?>">年级<?php if($key_name=='nGrade'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='nGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
				<div class="th"><a href="<?php echo U('Vip/Weixin/newMyStudents',array('key_name'=>'sClassAdviserCode','order'=>'asc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></div>
			<?php endif;?>
		</div>
		<?php if($myStudentList):?>
		<?php foreach($myStudentList as $key=>$myStudent):?>
			<div class="tr <?php if($key>=10):?>hide<?php endif;?>">
				<a href="<?php echo U('Vip/Weixin/newStudentInfo',array('student_code'=>$myStudent['sstudentcode'],'kecheng_code'=>$myStudent['skechengcode'],'lesson'=>$myStudent['nlessonno']));?>">
					<div class="td cOrange"><?php echo $myStudent['sstudentname'];?></div>
					<div class="td"><?php echo $myStudent['gradename'];?></div>
					<div class="td"><?php echo $myStudent['sclassadvisername'];?></div>
					<div class="clearit"><?php echo $myStudent['deptname'];?>，未上<?php echo $myStudent['nobegin_count'];?>次课（已上<?php echo $myStudent['end_count'];?>）</div>
				</a>
			</div>
		<?php endforeach?>
		<?php endif;?>
		<div class="loading" style="display:none"><i></i></div><br>
		<div class="more" onclick="loadingData()">点击查看更多</div>
	</div>
</article>
<script>
function loadingData(){
	$("div .hide").each(function(index){
		if(index<10){
			$(this).slideDown();
			$(this).removeClass('hide');
		}
	});
	if($('div .hide').length==0){
		$('.more').hide();
	}
}
</script>
</body>
</html>