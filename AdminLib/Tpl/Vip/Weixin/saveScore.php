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
<script src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/js/vip_wx.js"></script>
</head>
<body>
<header class="header">
	<h1>上传成绩</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<form id="confirmItembank" name="confirmItembank" method="POST">
		<div class="upPhoto">
			<div class="modCon">
					<dl>
						<dt>请选择学员姓名：</dt>
						<dd>
							<span class="selbar">
								<select id="student_code" name="student_code" >
									<option value="">请选择学员</option>
									<?php if($studentList):?>
									<?php foreach ($studentList as $key=>$student):?>
									<option value="<?=$student['sstudentcode']?>"><?=$student['sstudentname']?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
							</span>
						</dd>
						<dt>请选择学科：</dt>
						<dd>
							<span class="selbar">
								<select id="subject_name" name="subject_name" >
									<option value="">请选择学科</option>
									<?php if($subjectList):?>
									<?php foreach ($subjectList as $key=>$subject):?>
									<option value="<?=$subject?>"><?=$subject?></option>
									<?php endforeach;?>
									<?php endif;?>
								</select>
							</span>
						</dd>
						<dt>单科成绩：</dt>
						<dd>
							<input type="text" id="score" name="score" value="" style="width:90%">分	
						</dd>
						<dt>单科提分：</dt>
						<dd>
							<input type="text" id="up_score" name="up_score" value="" style="width:90%">分
						</dd>
						<dt>中考总分：</dt>
						<dd>
							<input type="text" id="total_score" name="total_score" value="" style="width:90%">分
						</dd>
					</dl>
					
			</div>
		
			<div class="modCon">
				<div class="button">
					<input type="hidden" id="user_key" name="user_key" value="<?=$userInfo['user_key']?>">
					<input type="hidden" id="teacher_name" name="teacher_name" value="<?=$userInfo['user_realname']?>">
					<input type="button" class="btn submit" value="确认提交" onclick="do_save_score()">
				</div>
			</div>	
		</div>
	</form>
</article>
</body>
</html>
<script type="text/javascript">
function do_save_score(){
	var student_code = $("#student_code").val();
	var student_name = $("#student_code").find("option:selected").text();
		var subject_name = $("#subject_name").val();
		var score = $("#score").val();
		var up_score = $("#up_score").val();
		var total_score = $("#total_score").val();
		var teacher_name = $("#teacher_name").val();
		var user_key = $("#user_key").val();
		if(student_code==''||subject_name==''||score==''||up_score==''||total_score==''||teacher_name==''){
			alert('请将成绩信息填写完整');
			return false;
		}else{
			$.post('<?php echo U('Vip/Weixin/doSaveScore')?>',
			{student_code:student_code,student_name:student_name,subject_name:subject_name,score:score,up_score:up_score,total_score:total_score,teacher_name:teacher_name,user_key:user_key},
			function(data){
				if(data.status==1){
					alert('提交成功');
					window.location.reload();
				}else if(data.status==-1){
					alert('该学员'+subject_name+'成绩已录入，不能重复录入');
				}else{
					alert('提交失败');
				}
				
			},'json');
		}
}


</script>