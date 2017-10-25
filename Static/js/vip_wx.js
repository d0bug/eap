function checkForm(formId){
	if(formId == 'confirm'){
		if($('#student_code').val() == ''){
			$('#confirm_msg').html('<font color=red>请选择学员</font>');
			return false;
		}
		if($('#kecheng_code').val() == ''){
			$('#confirm_msg').html('<font color=red>请选择课程</font>');
			return false;
		}
		if($('#helu_id').val() == ''){
			$('#confirm_msg').html('<font color=red>请选择上课时间</font>');
			return false;
		}
	}
}

function adjustKecheng(timeStr,helu_id,student_name,kecheng_name){
	$('#student_name').html(student_name);
	$('#kecheng_name').html(kecheng_name);
	$('#timeStr').html(timeStr);
	$('#helu_id').val(helu_id);
	$('#popWindow2').show();
	$('#popBg').show().css('height', $('html').height()+'px');
}

function doAdjust(requestUrl){
	if($("#date").val()!='' && $("#start").val()!='' && $("#end").val()!=''){
		var startArr = $("#start").val().split(":");
		var endArr = $("#end").val().split(":");
		if(parseInt(startArr[0])<parseInt(endArr[0]) || (parseInt(startArr[0])==parseInt(endArr[0]) && parseInt(startArr[1])<parseInt(endArr[1]) ) ){
			if(confirm('确定要调整课次时间吗？')){
				$.post(requestUrl,
				{helu_id:$("#helu_id").val(),date:$("#date").val(),start:$("#start").val(),end:$("#end").val()},
				function(data){
					var obj = eval('(' + data + ')');
					if(obj.status == 1){
						$("#adjust_msg").html('<font color=green>'+obj.msg+'</font>');
						setTimeout(function(){location.reload()}, 1000);
					}else{
						$("#adjust_msg").html('<font color=red>'+obj.msg+'</font>');
					}
				}
				);
			}
		}else{
			$("#adjust_msg").html('<font color=red>上课时间结束时间必须大于开始时间</font>');
		}
	}else{
		$("#adjust_msg").html('<font color=red>请选择调课后时间</font>');
	}
}


function addKecheng(requestUrl){
	$.get(requestUrl,
	function(data){
		$('#student_code').html(data);
		$('#popWindow').show();
		$('#popBg').show().css('height', $('html').height()+'px');
		return false;
	});
}


function get_kechengList(stuCode,teacherCode,requestUrl){
	if(stuCode!='' && teacherCode!=''){
		$.ajax({
			type: "GET",
			url: requestUrl,
			data:{stuCode:stuCode,teacherCode:teacherCode},
			beforeSend: function(data){
				handleblockUI();
			},
			success: function(data){
				$('#kecheng_code').html(data);
				handleunblockUI();
			},
		});
	}
}


function doAddKecheng(requestUrl){
	if($("#student_code").val()!='' && $("#kecheng_code").val()!='' && $("#date2").val()!='' && $("#start2").val()!='' && $("#end2").val()!=''){
		var startArr2 = $("#start2").val().split(":");
		var endArr2 = $("#end2").val().split(":");
		if(parseInt(startArr2[0])<parseInt(endArr2[0]) || (parseInt(startArr2[0])==parseInt(endArr2[0]) && parseInt(startArr2[1])<parseInt(endArr2[1]) ) ){
			if(confirm('确定要加课吗？')){
				$.post(requestUrl,
				{student_code:$("#student_code").val(),kecheng_code:$("#kecheng_code").val(),date:$("#date2").val(),start:$("#start2").val(),end:$("#end2").val()},
				function(data){
					var obj = eval('(' + data + ')');
					if(obj.status == 1){
						$("#add_msg").html('<font color=green>'+obj.msg+'</font>');
						setTimeout(function(){location.reload()}, 1000);
					}else{
						$("#add_msg").html('<font color=red>'+obj.msg+'</font>');
					}
				}
				);
			}
		}else{
			$("#add_msg").html('<font color=red>上课时间结束时间必须大于开始时间</font>');
		}
	}else{
		$("#add_msg").html('<font color=red>请选择学员、课程、上课时间</font>');
	}
}


function get_lessonList(kechengCode,teacherCode,requestUrl){
	var studentCode = $('#student_code').val();
	$.ajax({
		type: "GET",
		url: requestUrl,
		data:{studentCode:studentCode,kechengCode:kechengCode,teacherCode:teacherCode},
		beforeSend: function(data){
			handleblockUI();
		},
		success: function(data){
			$('#helu_id').html(data);
			handleunblockUI();
		},
	});
}


function del_wxImg(requestUrl){
	$.post(requestUrl,
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.status == 1){
			alert('图片删除成功');
			$('.photoList').html(obj.html);
		}else{
			alert('图片删除失败');
		}
	}
	);
}


function windowShow(windowid,msg,title,buttonText){
	$(windowid).show();
	$('#title').html(title);
	$('#button').html(buttonText);
	$('#error_msg').html(msg);
	$('#popBg').show().css('height', $('html').height()+'px');
}


function checkProgramForm(formId,requestUrl){
	if(formId == 'confirm'){
		if($('#student_code').val() == ''){
			$('#confirm_msg').html('<font color=red>请选择学员</font>');
			return false;
		}
		if($('#kecheng_code').val() == ''){
			$('#confirm_msg').html('<font color=red>请选择课程</font>');
			return false;
		}
		$.post(requestUrl,
		{student_code:$('#student_code').val(),student_name:$('#student_code option:selected').text(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_code option:selected').text(),teacher_code:$('#teacher_code').val(),teacher_name:$('#teacher_name').val(),from_type:$('#from_type').val()},
		function(data){
			var obj = eval('(' + data + ')');
			alert(obj.msg);

			if(obj.status == 1){
				window.location.href="/vip/weixin/myStudents";
			}
		}
		);
	}
}

//ajax交互时锁定页面
function handleblockUI() {
	$.blockUI({
		message: '<img src="/static/images/loading.png" />&nbsp;&nbsp;<strong>请稍候</strong>',
		showOverlay: true,
		css:{
		'background-clip': 'padding-box',
		'border-color': '#FFFFFF',
		'border': '1px solid rgba(0, 0, 0, 0.3)',
		'border-radius': '3px',
		'box-shadow': '0 3px 7px rgba(0, 0, 0, 0.3)',
		'padding':	'10px',
		'font-size':	'22px',
		'line-height':	'50px',
		'left': ($(window).width() - 400) /2 + 'px',
		'width': '400px'
		},
		overlayCSS: {
		'backgroundColor': '#999999'
		}
	});
}

//ajax完成后解锁页面
function handleunblockUI() {
	$.unblockUI();
}


function do_overdue(requestUrl,windowId,titleId,errorId,ispost){
	if(ispost ==1){
		$.post(requestUrl,
		function(data){
			var obj = eval('(' + data + ')');
			if(obj.status == 1){
				$(titleId).html('逾期未核录');
				$(errorId).html('超过48小时的课次不能进行核录');
				$(windowId).show();
			}
		}
		);
	}else{
		$(titleId).html('逾期未核录');
		$(errorId).html('超过48小时的课次不能进行核录');
		$(windowId).show();
	}
	
}



function show_report(url){
	window.open(url+"?time="+(new Date()).getTime());
}


function save_form1(requestUrl){
	var helu_id = $('#helu_id').val();
	var module_count = $('#module_count').val();
	var module_answer = '';
	if(module_count>0){
		for(var i=0;i<module_count;i++){
			var temp = '';
			$("input[name='module_"+i+"_[]']:radio").each(function(){
				if($(this).attr("checked")){
					temp = $(this).val();
				}
			});
			if(temp == undefined || temp == ''){
				alert('请选择例题中第'+(i+1)+'题的答题情况');
				return false;
			}else{
				module_answer += temp+'|';
			}
		}
		//alert('例题答题情况：'+module_answer);
	}

	var practise_count = $('#practise_count').val();
	var practise_answer = '';
	if(practise_count>0){
		for(var i=0;i<practise_count;i++){
			var temp = '';
			$("input[name='practise_"+i+"_[]']:radio").each(function(){
				if($(this).attr("checked")){
					temp = $(this).val();
				}
			});
			if(temp == undefined || temp == ''){
				alert('请选择随堂练习中第'+(i+1)+'题的答题情况');
				return false;
			}else{
				practise_answer += temp+'|';
			}
		}
		//alert('随堂练习答题情况：'+practise_answer);
	}


	var lastwork_count = $('#lastwork_count').val();
	var lastwork_answer = '';
	if(lastwork_count>0){
		for(var i=0;i<lastwork_count;i++){
			var temp = '';
			$("input[name='lastwork_"+i+"_[]']:radio").each(function(){
				if($(this).attr("checked")){
					temp = $(this).val();
				}
			});
			if(temp == undefined || temp == ''){
				alert('请选择上次作业中第'+(i+1)+'题的答题情况');
				return false;
			}else{
				lastwork_answer += temp+'|';
			}
		}
	}

	//alert('上次作业答题情况：'+lastwork_answer);
	
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{helu_id:$('#helu_id').val(),student_code:$('#student_code').val(),student_name:$('#student_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:$('#lesson_no').val(),lesson_date:$('#lesson_date').val(),lesson_begin:$('#lesson_begin').val(),lesson_end:$('#lesson_end').val(),module_answer:module_answer,practise_answer:practise_answer,lastwork_answer:lastwork_answer,last_helu_id:$('#last_helu_id').val()},
		beforeSend: function(data){
			handleblockUI();
		},
		success:function(data){
			handleunblockUI();
			var obj = eval('(' + data + ')');
			//alert(obj.msg);
			if(obj.status == 1){
				window.location.href = obj.url;
			}
			
		}
	});

}
