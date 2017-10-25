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
/*	else if(event.is_begin==1){
		messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;">课次已开始，不能进行调课</div>';
		is_reload = 0;
	} */
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	$('#calendar').html("");
	$('#calendar').fullCalendar({
		header: {
			left: ' today',
			center: 'prev,title,next',
			right: ''
		},
		defaultView : 'agendaWeek',
		editable: false,
		firstDay: '1',
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			var objPos = mousePosition(this);
			messContent='<div class="mesWindowsBox" style="height:300px;padding:20px;">';
			$.get('<?php echo U('Vip/VipSchedule/getAllStudents')?>',
			function(data){
				var start_str = start.getFullYear()+'-'+format_timeNumber(start.getMonth()+1)+'-'+format_timeNumber(start.getDate())+' '+format_timeNumber(start.getHours())+':'+format_timeNumber(start.getMinutes())+':'+format_timeNumber(start.getSeconds());
				var end_str = end.getFullYear()+'-'+format_timeNumber(end.getMonth()+1)+'-'+format_timeNumber(end.getDate())+' '+format_timeNumber(end.getHours())+':'+format_timeNumber(end.getMinutes())+':'+format_timeNumber(end.getSeconds());
				messContent +='<p>选择学员：<div id="studentHtml" class="m_left_70">'+data+'</div></p>';
				messContent +='<p>选择课程：<div id="kechengHtml" class="m_left_70"></div></p>';
				messContent +='<p><input type="hidden" id="student_code" name="student_code" value=""><input type="hidden" id="kecheng_code" name="kecheng_code" value=""></p><br>';
				messContent +='<p>上课时间：<input type="text" id="start" name="start" value="'+start_str+'" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"'+$('#now').val()+'"});\' class="Wdate">至<input type="text" id="end" name="end" value="'+end_str+'" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"#F{$dp.$D(start,{m:+30})}"});\' class="Wdate"></p><br>';
				messContent +='<p><input type="button" value="确定加课" class="btn m_left_70" onclick="add_kecheng(\'<?php echo U('Vip/VipSchedule/addKecheng',array('teacher_code'=>$userInfo['sCode']))?>\')"><span id="add_msg" class="error"></span></p></div>';
				showMessageBox('加课',messContent,objPos,700,1);
			}
			);

		},
		loading:function(isLoading, view){
			$('#w_or_m').html("本周");
			if(isLoading==true){
				$("#total_detail").html('共0人，0小时');
			}
		},
		events:'<?php echo U('Vip/VipSchedule/getSchedule',array('schedule'=>'week'));?>',
		eventAfterRender : function(event, element, view) {
			element.html('<a href="<?php echo U('Vip/VipStudents/newStudentLesson');?>/student_code/'+event.stuCode+'">'+event.title + '</a><br>' + event.classTimeCir+ '<br>' +event.sAreaName);
			if(event.bgcolor == 'bg-yellow'){
				element.html(element.html()+'<div class="arrow"><img src="/static/images/arrow.png"></div>');
			}
			element.addClass(event.bgcolor);
			$("#total_detail").html('共'+event.total_students+'人，'+event.total_hours+'小时');
			$("#print").html('<input type=button value="　打印　" onclick="javascript:window.print();">');
		},
		
		eventClick: function(event) {
			$("#test").val(event.title);
			var objPos = mousePosition(event);
			if(event.is_end==1){
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;">课次已结束，不能进行调课</div>';
				is_reload = 0;
			}else{
				is_reload = 1;
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;"><table class="tableInfo">'+
				'<tr><td>&nbsp;排课时间：</td><td>'+event.dateReal+'&nbsp;&nbsp;'+event.classTimeCir+'</td></tr>'+
				'<tr><td><font color=red>*</font>调课时间：</td><td><input type="text" id="start" name="start" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"'+event.now+'",maxDate:"'+event.max+'"});\' class="Wdate" onfocus="javascript:this.blur()">'+
				'至<input type="text" id="end" name="end" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"#F{$dp.$D(start,{m:+30})}",maxDate:"'+event.max+'"});\' class="Wdate" onfocus="javascript:this.blur()"></td></tr>'+
				'<tr><td><input type="hidden" id="helu_id" name="helu_id" value="'+event.helu_id+'"></td>'+
				'<td><input type=button value="确定调课" class="btn" onclick="adjust_kecheng(\'<?php echo U('Vip/VipSchedule/adjustKecheng')?>\')"><span id="adjust_msg" class="error"></span></td></tr></table></div>';
			}
			showMessageBox('调课',messContent,objPos,700,is_reload);
		},
		
	});
	
});

</script>
</head>
<body>
<div region="center">
<div id="main">
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="#none">周课表</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipSchedule/monthSchedule')?>">月课表</a>
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