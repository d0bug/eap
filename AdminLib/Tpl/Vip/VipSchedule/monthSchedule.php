<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href='/static/js/fullcalendar/fullcalendar_default.css' rel='stylesheet' />
<link href='/static/js/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='/static/js/fullcalendar/fullcalendar_eap.js'></script>
<link href="/static/js/fancybox/jquery.fancybox.css" rel="stylesheet" />
<script src="/static/js/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function() {
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	$('#calendar').fullCalendar({
		header: {
			left: ' today',
			center: 'prev,title,next',
			right: ''
		},
		defaultView : 'month',
		editable: false,
		firstDay: '1',
		/*selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			var objPos = mousePosition(this);
			messContent='<div class="mesWindowsBox" style="height:300px;padding:20px;">';
			$.get('<?php echo U('Vip/VipSchedule/getAllStudents')?>',
			function(data){
				messContent +='<p>选择学员：<div id="studentHtml">'+data+'</div></p>';
				messContent +='<p><input type="hidden" id="student_code" name="student_code" value=""><input type="hidden" id="kecheng_code" name="kecheng_code" value=""><input type="hidden" id="max_lesson" name="max_lesson" value=""></p><br>';
				messContent +='<p>上课时间：<input type="text" id="start" name="start" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:"'+$('#now').val()+'"});\' class="Wdate">至<input type="text" id="end" name="end" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:"#F{$dp.$D(start,{m:+30})}"});\' class="Wdate"></p><br>';
				messContent +='<p><input type="button" value="确定加课" class="btn m_left_70" onclick="add_kecheng(\'<?php echo U('Vip/VipSchedule/addKecheng',array('teacher_code'=>$userInfo['sCode']))?>\')"><span id="add_msg" class="error"></span></p></div>';
				showMessageBox('加课',messContent,objPos,700,1);
			}
			);
		},*/
		loading:function(isLoading, view){
			$('#w_or_m').html("本月");
			if(isLoading==true){
				$("#total_detail").html('共0人，0小时');
			}
		},
		events:'<?php echo U('Vip/VipSchedule/getSchedule',array('schedule'=>'month'));?>',
		eventAfterRender : function(event, element, view) {
			element.html('<a href="<?php echo U('Vip/VipStudents/newStudentLesson');?>/student_code/'+event.stuCode+'">'+event.title + '</a>&nbsp;' +event.nHours+'h');
			element.addClass(event.bgcolor);
			$("#total_detail").html('共'+event.total_students+'人，'+event.total_hours+'小时');
			$("#print").html('<input type=button value="　打印　" onclick="javascript:window.print();">');
		},
		/*
		eventClick: function(event) {
			$("#test").val(event.title);
			var objPos = mousePosition(event);
			if(event.is_end==1){
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;">课次已结束，不能进行调课</div>';
				is_reload = 0;
			}else if(event.is_begin==1){
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;">课次已开始，不能进行调课</div>';
				is_reload = 0;
			}else{
				is_reload = 1;
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;"><table class="tableInfo">'+
				'<tr><td>&nbsp;排课时间：</td><td>'+event.dateReal+'&nbsp;&nbsp;'+event.classTimeCir+'</td></tr>'+
				'<tr><td><font color=red>*</font>调课时间：</td><td><input type="text" id="start" name="start" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:"'+event.now+'"});\' class="Wdate">'+
				'至<input type="text" id="end" name="end" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:"#F{$dp.$D(start,{m:+30})}"});\' class="Wdate"></td></tr>'+
				'<tr><td><input type="hidden" id="student_code" name="student_code" value="'+event.stuCode+'"><input type="hidden" id="kecheng_code" name="kecheng_code" value="'+event.keCode+'"><input type="hidden" id="lesson" name="lesson" value="'+event.lesson+'"></td>'+
				'<td><input type=button value="确定调课" class="btn" onclick="adjust_kecheng(\'<?php echo U('Vip/VipSchedule/adjustKecheng')?>\')"><span id="adjust_msg" class="error"></span></td></tr></table></div>';
			}
			showMessageBox('调课',messContent,objPos,700,is_reload);
		},*/
	});
});

</script>
</head>
<body>
<div region="center">
<div id="main">
	<div class="tableTab">
		<ul class="tab">
			<li >
				<a href="<?php echo U('Vip/VipSchedule/index')?>">周课表</a>
			</li>
			<li class="current">
				<a href="#none">月课表</a>
			</li>
		</ul>
	</div>
	<input type="hidden" id="year" value="<?php echo $year;?>">
	<input type="hidden" id="month" value="<?php echo $month;?>">
	<input type="hidden" id="day" value="<?php echo $day;?>">
	<input type="hidden" id="now" value="<?php echo $year;?>">
	<div id="calendar"></div>
</div>
</div>
</body>
</html>