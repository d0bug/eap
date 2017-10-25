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
<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/static/js/iscroll.js"></script>
<script type="text/javascript" src="/static/js/vip_wx.js"></script>
</head>
<body>
<header class="header">
	<h1>核录<?php echo $heluInfo['student_name']?>同学</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<div class="heluInfo">
		<form id="helu" name="helu" method="POST">
		<input type="hidden" id="is_send_sms" name="is_send_sms" value="<?php echo $heluInfo['is_send_sms'];?>">
		<input type="hidden" id="helu_id" name="helu_id" value="<?php echo $heluInfo['helu_id'];?>">
		<input type="hidden" id="id" name="id" value="<?php echo $heluInfo['id'];?>">
		<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $heluInfo['kecheng_code'];?>">
		<input type="hidden" id="lesson_no" name="lesson_no" value="<?php echo $heluInfo['lesson_no'];?>">
		<input type="hidden" id="student_code" name="student_code" value="<?php echo $heluInfo['student_code'];?>">
		<input type="hidden" id="student_name" name="student_name" value="<?php echo $heluInfo['student_name'];?>">
		<input type="hidden" id="lesson_date" name="lesson_date" value="<?php echo $heluInfo['lesson_date'];?>">
		<input type="hidden" id="lesson_begin" name="lesson_begin" value="<?php echo $heluInfo['lesson_begin'];?>">
		<input type="hidden" id="lesson_end" name="lesson_end" value="<?php echo $heluInfo['lesson_end'];?>"  >
			<h3 class="modTit">课时基本信息：</h3>
			<div class="modCon">
				<dl class="modInfo">
					<dt>学员姓名：</dt>
					<dd><?php echo $heluInfo['student_name']?></dd>
					<dt>上课时间：</dt>
					<dd><?php echo $heluInfo['lesson_date']?> <?php echo $heluInfo['lesson_begin']?>~<?php echo $heluInfo['lesson_end']?></dd>
					<dt>课次：</dt>
					<dd><?php echo $heluInfo['lesson_no']?></dd>
				</dl>
			</div>
			
			<h3 class="modTit">上课主题：<em class="cOrange">必填</em></h3>
			<div class="modCon">
				<input type="text" id="lesson_topic" name="lesson_topic" value="<?php echo $heluInfo['lesson_topic']?>" size="60">
			</div>

			<h3 class="modTit">课堂评价：<em class="cOrange">必填</em></h3>
			<div class="modCon">
				<?php if(empty($heluInfo['comment'])):?>
					<textarea id="comment" name="comment" cols="30" rows="5" style="height: 400px; font-size: 14px;"><?php echo date('Y/m/d',strtotime($heluInfo['lesson_date'])) ?> <?php echo $userInfo['real_name']?>老师课堂评价:</textarea>
				<?php else:?>
					<textarea id="comment" name="comment" cols="30" rows="5" style="height: 400px; font-size: 14px;"><?php echo $heluInfo['comment']?></textarea>
				<?php endif;?>
			</div>

			<!-- <div class="msg">讲义图片可使用聊天界面直接发图，已上传讲义图片<span class="cOrange"><?php echo $wxImgNum?></span>张。</div> -->
			<!--<div style="margin: 10px 15px 0;padding: 6px 10px;font-size: .875rem;color:red">注意：每节课的课评仅可发送一次短信</div>-->
			<div class="button">
				<input type="button" class="btn" value="保存修改" id="saveButton" style="float:left">&nbsp;&nbsp;&nbsp;<!--<input type="button" class="<?php if($heluInfo['is_send_sms'] == 1):?>gray-btn<?php else:?>btn<?php endif;?>" value="提交并发送短信" id="submitButton" <?php if($heluInfo['is_send_sms'] == 1):?>disabled="disabled"<?php endif;?> style="float:right">-->
			</div>
		</form>
	</div>
</article>
<!-- 弹出层 -->
<div id="popWindow">
	<div class="popHelu">
		<div class="popHd" id="title">课堂评价提交失败！</div>
		<div class="popBd">
			<p id="error_msg">不能少填东西呢，课堂评价要<span class="cOrange">20字</span>以上哦~</p>
			<div class="button">
				<button type="button" class="btn" id="button">我错了</button>
			</div>
		</div>
	</div>
</div>
<div id="popBg"></div>
<!-- // 弹出层 -->
<script>
$(function() {
	$('#submitButton').click(function() {
		if($('#helu_id').val()=='' || $('#kecheng_code').val()=='' ||$('#lesson_no').val()==''||$('#student_code').val()==''||$('#student_name').val()==''||$('#lesson_date').val()==''||$('#lesson_begin').val()==''||$('#lesson_end').val()==''){
			windowShow('#popWindow','非法操作','课堂评价提交失败！','我错了');
			return false;
		}else if($('#lesson_topic').val()==''){
			windowShow('#popWindow','上课主题不能为空','课堂评价提交失败！','我错了');
			return false;
		}else if($('#comment').val()==''){
			windowShow('#popWindow','课堂评价不能为空','课堂评价提交失败！','我错了');
			return false;
		}else if($('#comment').val().length<70){
			windowShow('#popWindow','课堂评价字数不能少于<span class="cOrange">70字</span>','课堂评价提交失败！','我错了');
			return false;
		}else{
			$.post('<?php echo U('Vip/Weixin/doHelu')?>',
			{act:'<?php echo $act?>',helu_id:$("#helu_id").val(),id:$("#id").val(),kecheng_code:$("#kecheng_code").val(),lesson_no:$("#lesson_no").val(),student_code:$("#student_code").val(),student_name:$("#student_name").val(),lesson_date:$("#lesson_date").val(),lesson_begin:$("#lesson_begin").val(),lesson_end:$("#lesson_end").val(),lesson_topic:$("#lesson_topic").val(),comment:$("#comment").val(),is_sendsms:1,is_send_sms:$('#is_send_sms').val()},
			function(data){
				var obj = eval('(' + data + ')');
				if(obj.status == 1){
					// windowShow('#popWindow','全职教师如未上传图片，不要忘记在课后<span class="cOrange">24小时</span>后登陆教师系统PC端，在<span class="cOrange">我的学员</span>中上传讲义呦~','课堂评价提交成功！','OK');
					windowShow('#popWindow','<p style="text-align:center">写课评，转课评，发图片。<br>用心服务，用爱教学！</p>','课堂评价保存成功！','OK');
				}else{
					windowShow('#popWindow','','课堂评价提交失败！','我错了');
				}
			}
			);
		}
	});


	$('#popWindow .btn').on('touchend', function() {
		$('#popWindow, #popBg').hide();
		if($('#button').html()=='OK'){
			<?php if($act == 'update'):?>
			window.location.href="<?php echo U('Vip/Weixin/kechengList',array('openid'=>$userInfo['openId'],'student_code'=>$heluInfo['student_code'],'kecheng_code'=>$heluInfo['kecheng_code'],'student_name'=>$heluInfo['student_name']))?>";
			<?php else:?>
			window.location.href="<?php echo U('Vip/Weixin/waitHelu')?>";
			<?php endif;?>
		}
	});
	
	
	$('#saveButton').click(function() {
		if($('#helu_id').val()=='' || $('#kecheng_code').val()=='' ||$('#lesson_no').val()==''||$('#student_code').val()==''||$('#student_name').val()==''||$('#lesson_date').val()==''||$('#lesson_begin').val()==''||$('#lesson_end').val()==''){
			windowShow('#popWindow','非法操作','课堂评价提交失败！','我错了');
			return false;
		}else if($('#lesson_topic').val()==''){
			windowShow('#popWindow','上课主题不能为空','课堂评价提交失败！','我错了');
			return false;
		}else if($('#comment').val()==''){
			windowShow('#popWindow','课堂评价不能为空','课堂评价提交失败！','我错了');
			return false;
		}else if($('#comment').val().length<70){
			windowShow('#popWindow','课堂评价字数不能少于<span class="cOrange">70字</span>','课堂评价提交失败！','我错了');
			return false;
		}else{
			$.post('<?php echo U('Vip/Weixin/doHelu')?>',
			{act:'<?php echo $act?>',helu_id:$("#helu_id").val(),id:$("#id").val(),kecheng_code:$("#kecheng_code").val(),lesson_no:$("#lesson_no").val(),student_code:$("#student_code").val(),student_name:$("#student_name").val(),lesson_date:$("#lesson_date").val(),lesson_begin:$("#lesson_begin").val(),lesson_end:$("#lesson_end").val(),lesson_topic:$("#lesson_topic").val(),comment:$("#comment").val(),is_sendsms:0,is_send_sms:$('#is_send_sms').val()},
			function(data){
				var obj = eval('(' + data + ')');
				if(obj.status == 1){
					// windowShow('#popWindow','全职教师如未上传图片，不要忘记在课后<span class="cOrange">24小时</span>后登陆教师系统PC端，在<span class="cOrange">我的学员</span>中上传讲义呦~','课堂评价保存成功！','OK');
					windowShow('#popWindow','<p style="text-align:center">写课评，转课评，发图片。<br>用心服务，用爱教学！</p>','课堂评价保存成功！','OK');
				}else{
					windowShow('#popWindow','','课堂评价保存失败！','我错了');
				}
			}
			);
		}
	});
});
</script>
</body>
</html>