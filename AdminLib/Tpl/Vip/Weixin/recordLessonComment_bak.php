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
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.raty.min2.js"></script>
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script src="/static/js/vip_wx.js"></script>
</head>
<body>
<header class="header">
	<h1>核录<?php echo $heluInfo['sstudentname']?>同学</h1>
	<div class="arr"></div>
</header>

<article class="wrap">
	<div class="heluInfo">
			<input type="hidden" id="act" name="act" value="<?php if(!empty($heluInfo['comment'])):?>update<?php else:?>add<?php endif;?>">
			<input type="hidden" id="helu_id" name="helu_id" value="<?php echo $heluInfo['helu_id']?>">
			<input type="hidden" id="student_code" name="student_code" value="<?php echo $heluInfo['sstudentcode']?>">
			<input type="hidden" id="student_name" name="student_name" value="<?php echo $heluInfo['sstudentname']?>">
			<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $heluInfo['skechengcode']?>">
			<input type="hidden" id="kecheng_name" name="kecheng_name" value="<?php echo $heluInfo['skechengname']?>">
			<input type="hidden" id="lesson_no" name="lesson_no" value="<?php echo $heluInfo['nlessonno']?>">
			<input type="hidden" id="lesson_date" name="lesson_date" value="<?php echo $heluInfo['dtdatereal']?>">
			<input type="hidden" id="lesson_begin" name="lesson_begin" value="<?php echo date('H:i',strtotime($heluInfo['dtlessonbeginreal']))?>">
			<input type="hidden" id="lesson_end" name="lesson_end" value="<?php echo date('H:i',strtotime($heluInfo['dtlessonendreal']))?>">
			<h3 class="modTit">课时基本信息：</h3>
			<div class="modCon">
				<dl class="modInfo">
					<dt>学员姓名：</dt>
					<dd><?php echo $heluInfo['sstudentname']?></dd>
					<dt>上课时间：</dt>
					<dd><?php echo date('Y-m-d',strtotime($heluInfo['dtdatereal']))?> <?php echo date('H:i',strtotime($heluInfo['dtlessonbeginreal']))?>~<?php echo date('H:i',strtotime($heluInfo['dtlessonendreal']))?></dd>
					<dt>课次：</dt>
					<dd><?php echo $heluInfo['nlessonno']?></dd>
				</dl>
			</div>
			
			<h3 class="modTit">上课主题：<em class="cOrange">必填</em></h3>
			<div class="modCon">
				<input type="text" id="lesson_topic" name="lesson_topic" value="<?php echo $heluInfo['lesson_topic']?>" placeholder="请输入课次主题"/>
			</div>

			<h3 class="modTit">课堂评价：<em class="cOrange">必填</em></h3>
			<ul class="reviews">
				<?php if($heluInfo['dimension']):?>
						<?php foreach ($heluInfo['dimension'] as $key=>$dimension):?>
							<li><?php echo $dimension['title']?>：<span id="raty_<?php echo $dimension['id']?>" ></span>
								<input type="hidden" name="dimension_id[]" value="<?php echo $dimension['id']?>">
								<input type="hidden" name="dimension_title[]" value="<?php echo $dimension['title']?>">
							</li>
							<script type="text/javascript">
							var _initRaty<?php echo $dimension['id']?> = function() {
								$('#raty_<?php echo $dimension['id']?>').raty({
									<?php if($dimension['level']):?>score:<?php echo $dimension['level']?>,<?php endif;?>
									number: <?php echo count($levelArr)?> ,
									hints: [
									<?php foreach ($levelArr as $key=>$level):?>
									'<?php echo $level['title']?>'<?php if($key != count($levelArr)-1):?>,<?php endif;?>
									<?php endforeach;?>
									],
								});

							}
							$(function() {
								_initRaty<?php echo $dimension['id']?>();
							})
							</script>
						<?php endforeach;?>
					<?php endif;?>
					<input type="hidden" id="dimension_count" name="dimension_count" value="<?php echo count($heluInfo['dimension'])?>">
			</ul>
			<div class="modCon">
				<textarea id="comment" name="comment"  cols="30" rows="5" placeholder="请输入课堂评价"><?php echo $heluInfo['comment']?></textarea>
			</div>

			<div class="msg">
			1、讲义图片可使用聊天界面直接发图，已上传讲义图片<span class="cOrange"><?php echo $wxImgNum?></span>张。<br>
			2、每节课的课评仅可发送一次短信。
			</div>

			<div class="button ">
				<button id="saveButton" class="btn" data-clipboard-target="comment">保存</button>	
				<!--<button id="submitButton" class="<?php if($heluInfo['is_send_sms'] == 1):?>gray-btn<?php else:?>btn<?php endif;?>" <?php if($heluInfo['is_send_sms'] == 1):?>disabled="disabled"<?php endif;?>>提交并发短信</button>-->	
				<div class="clear"></div>		
			</div>	
	</div>
</article>

<!-- 弹出层 -->
<div id="popWindow">
	<div class="popHelu">
		<div class="popHd">课堂评价提交失败！</div>
		<div class="popBd">
			<p>不能少填东西呢，课堂评价要<span class="cOrange">70字</span>以上哦~</p>
			<div class="button">
				<button type="button" class="btn">我错了</button>
			</div>
		</div>
	</div>
</div>

<div id="popWindow2">
	<div class="popHelu">
		<div class="popHd">以下三项都完成了吗？</div>
		<div class="popBd">
			<p class="center">
			1、记录上课轨迹；<br>
			2、填写课堂评价；<br>
			3、上传三张轨照。<br>
			</p>
			<div class="button">
				<button type="button" class="btn btnfl" >继续核录</button>
				<button type="button" class="btn btnfr" onclick="">生成学习报告</button>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>

<div id="popBg"></div>
<!-- // 弹出层 -->

<script>
$(function() {

	$('#popWindow .btn,#popWindow2 .btnfl').on('click', function() {
		$('#popWindow, #popBg,#popWindow2, #popBg').hide();
	});

	$('#popWindow2 .btnfr').on('click', function() {
		$.ajax({
			type: 'POST',
			url: '<?php echo U('Vip/Weixin/createLessonReport')?>',
			data:{helu_id:<?php echo $heluInfo['helu_id']?>,from:'wx',teacher_name:'<?php echo $heluInfo['steachername']?>'},
			beforeSend: function(data){
				handleblockUI();
			},
			success:function(data){
				handleunblockUI();
				var obj = eval('(' + data + ')');
				if(obj.status==1){
					//alert('学习报告生成成功');
					window.location.href = obj.report_url_wx+"?time="+(new Date()).getTime();
				}else{
					alert('学习报告生成失败');
					windowShow('#popWindow','操作失败','学习报告生成失败！','知道了');
				}
			}
		});
		//$('#popWindow, #popBg,#popWindow2, #popBg').hide();
	});


	/*提交并发短信*/
	$('#submitButton').click(function() {
		//$('#popBg').show().css('height', $('html').height()+'px');
		if($('#helu_id').val()==''){
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
			var dimension_id_str = '';
			$("input[name='dimension_id[]']").each(function(index, element) {
				dimension_id_str += $(this).val()+"|"
			});
			var dimension_title_str = '';
			$("input[name='dimension_title[]']").each(function(index, element) {
				dimension_title_str += $(this).val()+"|"
			});
			var level_str = '';
			$("input[name='score[]']").each(function(index, element) {
				level_str += $(this).val()+"|"
			});
			var reg=new RegExp("\n","g");
			var comment = $('#comment').val().replace(reg,"<br>");
			var is_send_sms = 1;
			if(dimension_id_str != '' && dimension_title_str != '' && level_str != ''){
				$.ajax({
					type: 'POST',
					url: '<?php echo U('Vip/Weixin/savePartTwo')?>',
					data:{act:$('#act').val(),helu_id:$('#helu_id').val(),student_code:$('#student_code').val(),student_name:$('#student_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:$('#lesson_no').val(),lesson_date:$('#lesson_date').val(),lesson_begin:$('#lesson_begin').val(),lesson_end:$('#lesson_end').val(),lesson_topic:$('#lesson_topic').val(),dimension_id_str:dimension_id_str,dimension_title_str:dimension_title_str,level_str:level_str,comment:comment,is_send_sms:is_send_sms},
					//beforeSend: function(data){
					//handleblockUI();
					//},
					success:function(data){
						//handleunblockUI();
						var obj = eval('(' + data + ')');
						if(obj.status == 1){
							$('#popWindow2').show();
						}else{
							windowShow('#popWindow','','课堂评价提交失败！','我错了');
						}
					}
				});
			}else{
				windowShow('#popWindow','请进行维度评价','课堂评价提交失败！','我错了');
				return false;
			}

		}
	});


	/*保存修改*/
	$('#saveButton').click(function() {
		saveInfo();
	});


function saveInfo(){
	//$('#popBg').show().css('height', $('html').height()+'px');
	if($('#helu_id').val()==''){
		windowShow('#popWindow','非法操作','课堂评价提交失败！','我错了');
		return false;
	}else if($('#lesson_topic').val()==''){
		windowShow('#popWindow','上课主题不能为空','课堂评价提交失败！','我错了');
		return false;
	}else if($('#comment').val()==''){
		windowShow('#popWindow','课堂评价不能为空','课堂评价提交失败！','我错了');
		return false;
	}else if($('#comment').val().length<20){
		windowShow('#popWindow','课堂评价字数不能少于<span class="cOrange">20字</span>','课堂评价提交失败！','我错了');
		return false;
	}else{
		var dimension_id_str = '';
		$("input[name='dimension_id[]']").each(function(index, element) {
			dimension_id_str += $(this).val()+"|"
		});
		var dimension_title_str = '';
		$("input[name='dimension_title[]']").each(function(index, element) {
			dimension_title_str += $(this).val()+"|"
		});
		var level_str = '';
		$("input[name='score[]']").each(function(index, element) {
			level_str += $(this).val()+"|"
		});
		var reg=new RegExp("\n","g");
		var comment = $('#comment').val().replace(reg,"<br>");
		var is_send_sms = 0;
		if(dimension_id_str != '' && dimension_title_str != '' && level_str != ''){
			$.ajax({
				type: 'POST',
				url: '<?php echo U('Vip/Weixin/savePartTwo')?>',
				data:{act:$('#act').val(),helu_id:$('#helu_id').val(),student_code:$('#student_code').val(),student_name:$('#student_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:$('#lesson_no').val(),lesson_date:$('#lesson_date').val(),lesson_begin:$('#lesson_begin').val(),lesson_end:$('#lesson_end').val(),lesson_topic:$('#lesson_topic').val(),dimension_id_str:dimension_id_str,dimension_title_str:dimension_title_str,level_str:level_str,comment:comment,is_send_sms:is_send_sms},
				beforeSend: function(data){
					handleblockUI();
				},
				success:function(data){
					handleunblockUI();
					var obj = eval('(' + data + ')');
					if(obj.status == 1){
						$('#popWindow2').show();
					}else{
						windowShow('#popWindow','','课堂评价提交失败！','我错了');
					}
				}
			});

		}else{
			windowShow('#popWindow','请进行维度评价','课堂评价提交失败！','我错了');
			return false;
		}

	}
}
});
</script>
</body>
</html>