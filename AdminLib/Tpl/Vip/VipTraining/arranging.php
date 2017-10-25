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
			$.get('<?php echo U('Vip/VipTraining/getArrTraList')?>',
			function(data){
			 var obj = eval('(' + data + ')');
				var start_str = start.getFullYear()+'-'+format_timeNumber(start.getMonth()+1)+'-'+format_timeNumber(start.getDate())+' '+format_timeNumber(start.getHours())+':'+format_timeNumber(start.getMinutes())+':'+format_timeNumber(start.getSeconds());
				var end_str = end.getFullYear()+'-'+format_timeNumber(end.getMonth()+1)+'-'+format_timeNumber(end.getDate())+' '+format_timeNumber(end.getHours())+':'+format_timeNumber(end.getMinutes())+':'+format_timeNumber(end.getSeconds());
                messContent = '<form id="dict-add-form" method="post" novalidate action="<?php echo U('Vip/VipTraining/addKecheng');?>" >';
			    messContent +='<div class="mesWindowsBox" style="height:350px;padding:20px;">';                 
                messContent +='<p>课程名称：<input type="text" id="ke_name" name="keName" value=""><span style="color:red">注:公共课一定要加【公共】二字</span></p>';
                messContent +='<p>讲师姓名：<input type="text" name="teac_name" value=""></p>';
                messContent +='<p>选择培训期：<div id="trainingHtml" class="m_left_70">'+obj.trainingHtml+'</div></p>';
                messContent +='<p>学科：<div id="xuekeHtml" class="m_left_70">'+obj.xuekeHtml+'</div></p>';
				messContent +='<p>上课时间：<input type="text" id="start" name="start" value="'+start_str+'" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"'+$('#now').val()+'"});\' class="Wdate">至<input type="text" id="end" name="end" value="'+end_str+'" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"#F{$dp.$D(start,{m:+30})}"});\' class="Wdate"></p><br>';
                messContent +='<p>上课地点：<input type="text" name="cl_address" value=""></p>';
				messContent +='<p><input type="submit" value="确定加课" class="btn m_left_70" ><span id="add_msg" class="error"></span></p></div>';
                messContent += '</form>';                
				showMessageBox('加课',messContent,objPos,800,1);
			}
			);

		},
		loading:function(isLoading, view){
			
			if(isLoading==true){
			}
		},		
       	events:'<?php echo U('Vip/VipTraining/getArrangingList',array('aranging'=>'week'));?>',
		eventAfterRender : function(event, element, view) {		  
			element.html(event.title + '<br>' + event.ar_teacher + '<br>' +event.class_address );
			if(event.bgcolor == 'bg-yellow'){
				element.html(element.html()+'<div class="arrow"><img src="/static/images/arrow.png"></div>');+ '<br>'+ event.classTimeCir
			}
			element.addClass(event.bgcolor);
		
		},
                
		eventClick: function(event) {
			$("#test").val(event.title);
			var objPos = mousePosition(event);
			if(event.is_end==1){
				messContent='<div class="mesWindowsBox" style="height:200px;padding:50px;">课次已结束，不能进行调课</div>';
				is_reload = 0;
			}else{
				is_reload = 1;
               
				messContent ='<div class="mesWindowsBox" style="height:500px;padding:30px;"><table class="tableInfo">'+
                	'<tr><td><input type=button value="删除此次课程" class="btn" onclick="deljust_kecheng(\'<?php echo U('Vip/VipTraining/deljust_kecheng')?>\')"><span id="adjust_msg" class="error"></span></td></tr>'+
                '<tr><td>&nbsp;培训期：</td><td>'+event.tr_name+'<td></tr>'+
                '<tr><td>&nbsp;参与上课的学科：</td><td>'+event.xueke+'<td></tr>'+
				'<tr><td>&nbsp;排课时间：</td><td>'+event.dateReal+'&nbsp;&nbsp;'+event.classTimeCir+'</td></tr>'+
				'<tr><td><font color=red>*</font>调课时间：</td><td><input type="text" id="start" name="start" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"'+$('#now').val()+'"});\' class="Wdate" onfocus="javascript:this.blur()">'+
				'至<input type="text" id="end" name="end" size="24" onClick=\'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:00",minDate:"'+$('#now').val()+'"});\' class="Wdate" onfocus="javascript:this.blur()"></td></tr>'+
                '<tr><td>课程名称：</td><td><input type="text" id="title" name="title" value="'+event.title+'"></td></tr>'+
                '<tr><td>讲师姓名：</td><td><input type="text" id="ar_teacher" name="ar_teacher" value="'+event.ar_teacher+'"></td></tr>'+
                '<tr><td>上课地点：</td><td><input type="text" id="class_address" name="class_address" value="'+event.class_address+'"></td></tr>'+				
                '<tr><td><input type="hidden" id="id" name="id" value="'+event.id+'"></td>'+
                '<td><input type="hidden" id="tr_id" name="tr_id" value="'+event.tr_id+'"></td></tr>'+                  
				'<tr><td><input type=button value="确定调课" class="btn" onclick="adtrjust_kecheng(\'<?php echo U('Vip/VipTraining/adjustKecheng')?>\')"><span id="adjust_msg" class="error"></span></td></tr></table></div>';
			}
			showMessageBox('调课',messContent,objPos,600,is_reload);
		},
		
	});
	
});

function adtrjust_kecheng(requestUrl){
	if($("#start").val()!='' && $("#end").val()!=''){
		if(confirm('确定要调整课次时间吗？')){
			$.post(requestUrl,
			{id:$("#id").val(),start:$("#start").val(),end:$("#end").val(),title:$("#title").val(),ar_teacher:$("#ar_teacher").val(),class_address:$("#class_address").val()},
			function(data){
				var obj = eval('(' + data + ')');
				alert(obj.msg);
				if(obj.status == 1){
					document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
					location.reload();
				}
			}
			);
		}
	}else{
		$("#adjust_msg").html('请选择调课后时间');
	}
}

function deljust_kecheng(requestUrl){
	if($("#id").val()!='' && $("#tr_id").val() != '' ){
		if(confirm('确定要删除此次课程吗？')){
			$.post(requestUrl,
			{id:$("#id").val(),tr_id:$("#tr_id").val()},
			function(data){
				var obj = eval('(' + data + ')');
				alert(obj.msg);
				if(obj.status == 1){
					document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
					location.reload();
				}
			}
			);
		}
	}else{
		$("#adjust_msg").html('无法删除此课程！');
	}
}

var selAll = document.getElementById("selAll");
function selectAll()
{
  var obj = document.getElementsByName("contain_module[]");
  console.log(obj);
  if(document.getElementById("selAll").checked == false)
  {
      for(var i=0; i<obj.length; i++){
        obj[i].checked=false;
      }
  }else{
          for(var i=0; i<obj.length; i++){  
            obj[i].checked=true;
          }
  }
 
} 





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