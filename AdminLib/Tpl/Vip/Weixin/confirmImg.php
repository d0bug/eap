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
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script src="/static/js/lightbox.js"></script>
<link href="/static/css/lightbox.css" rel="stylesheet" />
<script src="/static/js/iscroll.js"></script>
<script src="/static/js/vip_wx.js"></script>

</head>
<body>
<header class="header">
	<h1>上传讲义</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<form id="confirm" name="confirm" method="POST" action="<?php echo U('Vip/Weixin/doConfirmImg')?>" onSubmit="return checkForm('confirm')">
		<div class="upPhoto">
			<h3 class="modTit">请选择本次上传图片所对应的课次：</h3>
			<div class="modCon">
					<dl>
						<dt>请选择学员姓名：</dt>
						<dd>
							<span class="selbar">
								<select id="student_code" name="student_code" onchange="get_kechengList(this.value,'<?php echo $userInfo['sCode']?>','<?php echo U('Vip/Weixin/getKechengList')?>');">
									<option value="">请选择学员</option>
								</select>
							</span>
						</dd>
						<dt>请选择课程：</dt>
						<dd>
							<span class="selbar">
								<select id="kecheng_code" name="kecheng_code" onchange="get_lessonList(this.value,'<?php echo $userInfo['sCode']?>','<?php echo U('Vip/Weixin/get_lessonList',array('selectAll'=>0))?>')">
									<option value="">请选择课程</option>
								</select>
							</span>
						</dd>
						<dt>请选择上课时间：</dt>
						<dd>
							<span class="selbar">
								<select id="helu_id" name="helu_id">
									<option value="">请选择上课时间</option>
								</select>
							</span>
						</dd>
					</dl>
					
				
			</div>
			<h3 class="modTit">您上传的图片如下：</h3>
			<div class="modCon">
				<div><font color=red>提醒：如果图片数量不符，请稍后再确认哦~</font></div>
			<?php if($wxImgList):?>
				<ul class="photoList">
				<?php foreach($wxImgList as $key=>$wxImg):?>
					<li><a href="<?php echo $wxImg['serviceurl_show']?>" rel="lightbox" id="ShowLightBox"><img src="<?php echo $wxImg['serviceurl_show']?>" /></a><a href="javascript:void(0)" onclick="del_wxImg('<?php echo U('Vip/Weixin/delWxImg',array('id'=>$wxImg['id']))?>')">删除照片</a></li>
				<?php endforeach?>
				</ul>
			<?php endif;?>
			</div>
			<div class="modCon">
				<div class="button">
					<input type="submit" class="btn" value="确认上传">
					<span id="confirm_msg" class="error"></span>
				</div>
			</div>
		</div>
	</form>
</article>
</body>
</html>
<script type="text/javascript">
$(function () {
	$.get('<?php echo U('Vip/Weixin/getAllStudents',array('jieke'=>0,'type'=>1))?>',
	function(data){
		$('#student_code').html(data);
	});
});


</script>