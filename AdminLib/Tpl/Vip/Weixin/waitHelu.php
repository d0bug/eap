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
<script src="/static/js/iscroll.js"></script>
<script type="text/javascript" src="/static/js/vip_wx.js"></script>
</head>
<body>
<header class="header">
	<h1>待核录学员</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<?php if(!empty($waitHeluList)):?>
	<ul class="heluList">
		<?php foreach($waitHeluList as $key=>$waitHelu):?>
		<?php if(empty($waitHelu['lesson_topic'])): ?>
			<?php if($waitHelu['overdue']==0):?>
				<li>
				<a href="<?php echo U('Vip/Weixin/keChengHeLu',array('act'=>'add','helu_id'=>$waitHelu['heluid'],'student_code'=>$waitHelu['sstudentcode'],'student_name'=>$waitHelu['sstudentname'],'kecheng_code'=>$waitHelu['skechengcode'],'lesson_no'=>$waitHelu['nlessonno'],'lesson_date'=>strtotime($waitHelu['dtdatereal']),'lesson_begin'=>strtotime($waitHelu['dtlessonbeginreal']),'lesson_end'=>strtotime($waitHelu['dtlessonendreal'])))?>"><div class="b">核录</div></a>
				<img src="/static/images/face.jpg" />
				<h3><?php echo $waitHelu['sstudentname']?></h3>
				<p><?php echo $waitHelu['dtdatereal']?>  <?php echo $waitHelu['dtlessonbeginreal']?>~<?php echo $waitHelu['dtlessonendreal']?></p>
				</li>
			<?php else: ?>
				<li>
				<a href="javascript:void(0)" onclick="do_overdue('<?php echo U('Vip/Weixin/doOverdue',array('helu_id'=>$waitHelu['heluid']))?>','#popWindow','#title','#error_msg',<?php if(empty($lesson['is_overdue'])):?>1<?php else:?>0<?php endif;?>)">
				<div class="b c">逾期</div>
				</a>
				<img src="/static/images/face.jpg" />
				<h3><?php echo $waitHelu['sstudentname']?></h3>
				<p><?php echo $waitHelu['dtdatereal']?>  <?php echo $waitHelu['dtlessonbeginreal']?>~<?php echo $waitHelu['dtlessonendreal']?></p>
				</li>
			<?php endif;?>
		<?php endif; ?>

		<?php endforeach?>
	</ul> 
	<?php endif;?>
</article>
<!-- 弹出层 -->
<div id="popWindow">
	<div class="popHelu">
		<div class="popHd" id="title"></div>
		<div class="popBd">
			<p id="error_msg"></p>
			<div class="button">
				<button type="button" class="btn" id="button">知道了</button>
			</div>
		</div>
	</div>
</div>
<div id="popBg"></div>
<!-- // 弹出层 -->
</body>
<script type="text/javascript">
$('#popWindow .btn').on('touchstart', function() {
	$('#popWindow, #popBg').hide();
	if($('#button').html()=='OK'){
		location.reload();
	}
});
</script>
</html>