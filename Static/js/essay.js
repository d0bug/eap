$(function () {
	$(".checkAll").click(function () {
		if(this.checked){
			$("input[name='excellent[]']:checkbox").attr("checked", true);
		}else{
			$("input[name='excellent[]']:checkbox").attr("checked", false);
		}
	});
});

function getChildren(obj,id,divId,topId,requestUrl,deep){
	$(obj).addClass("hover");
	if(id != ''){
		$.get(requestUrl,
		{pid:id,topId:topId,deep:deep},
		function(data){
			var obj = eval('(' + data + ')');
			$(divId).html(obj.typeHtml);
			$("#theme").html(obj.themeHtml);
		}
		);
	}else{
		alert('非法操作');
	}
}

function selectType(level,name){
	var inputId;
	if(level == 1){
		$("#child").html('');
		$("#grandson").html('');
		$("#four").html('');
		$("#typeOne").val(name);
		$("#typeTwo").val('');
		$("#typeThree").val('');
		$("#typeFour").val('');
		$("#themeName").val('');
		$("#parent li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if(level == 2){
		$("#grandson").html('');
		$("#four").html('');
		$("#typeTwo").val(name);
		$("#typeThree").val('');
		$("#typeFour").val('');
		$("#themeName").val('');
		$("#child li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if(level == 3){
		$("#four").html('');
		$("#typeThree").val(name);
		$("#typeFour").val('');
		$("#themeName").val('');
		$("#grandson li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if(level == 4){
		$("#typeFour").val(name);
		$("#four li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if(level == 9){
		$("#themeName").val(name);
		$("#theme li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if(level == 8){
		$("#essayLength").val(name);
		$("#legnth li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
	if($("#typeOne").val() != '记事的'){
		$("#themeName").val('');
		$("#input_theme").hide();
	}else{
		$("#input_theme").show();
	}

	if(level == 'student'){
		$('#studentInfo').val(name);
		$(".Students li").each(function(i,val){
			$(this).removeClass('bgcolor');
		});
	}
}


function checkInfo(essay_length,type_one,type_two,type_three,type_four,theme_name){
	if($("#essayLength").val() == ''){
		$("#return_msg").html('请选择篇幅;');
		return false;
	}
	if($("#typeOne").val() == ''){
		$("#return_msg").html('请选择一级分类;');
		return false;
	}
	if($("#typeTwo").val() == ''){
		$("#return_msg").html('请选择二级分类');
		return false;
	}
	if($("#grandson").html().replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '') != ''){
		if($("#typeThree").val() == ''){
			$("#return_msg").html('请选择三级分类');
			return false;
		}
	}
	if($("#four").html().replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '') != ''){
		if($("#typeFour").val() == ''){
			$("#return_msg").html('请选择四级分类');
			return false;
		}
	}
	if($("#typeOne").val() == '记事的'){
		if($("#themeName").val() == ''){
			$("#return_msg").html('请选择主题分类');
			return false;
		}
	}

	if(essay_length == $("#essayLength").val() && type_one ==$("#typeOne").val() && type_two == $("#typeTwo").val() && type_three==$("#typeThree").val()&& type_four==$("#typeFour").val()&&theme_name==$("#themeName").val()){
		$("#return_msg").html('您未做任何修改，无需保存');
		return false;
	}
	return true;
}

function selectLessonNo(lessonno,class_code){
	$(".Nums span").each(function(i,val){
		$(this).removeClass('bgcolor');
	});
	$("#classInfo_"+class_code).attr("checked",true);
	$("#speakerNumber_"+class_code).val(lessonno);
}


function change_classCode(className,requestUrl){
	$.get(requestUrl,
	{className:className},
	function(data){
		$("#classCode").html(data);
	}
	);
}

function checkSelectClassForm(requestUrl){
	if($("input[name='classInfo']:checked").val() == undefined){
		$("#return_msg").html('请选择班级信息');
		return false;
	}
	var classInfo = $("input[name='classInfo']:checked").val();
	var classInfoArr = classInfo.split("|");
	if($("#speakerNumber_"+classInfoArr[1]).val() == ''){
		$("#return_msg").html('请选择讲次');
		return false;
	}
}

function checkAddEssayForm(){
	if($("#studentInfo").val()==''){
		$("#return_msg").html('请选择学生');
		return false;
	}
	if($("#essayImgs").val()==''){
		$("#return_msg").html('请上传作文照片');
		return false;
	}
}


function del_img(requestUrl,imgUrl,key,essayId){
	if(confirm('确定要删除照片吗？')){
		$.get(requestUrl,
		{url:imgUrl,essayId:essayId,essayImgs:$("#essayImgs").val()},
		function(data){
			var obj = eval('(' + data + ')');
			$("#essayImgs").val(obj.essay_imgs);
			if(obj.status == 1){
				$("#pre_"+key).remove();
			}
		}
		);
	}else{
		return false;
	}
}


function do_excellent(requestUrl,act,class_code,speaker_number,essay_id){
	var essay_id_str='';
	if(act == 'delete'){
		$("input[name='excellent[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				essay_id_str += $(this).val()+"|";
			}
		})
		if(essay_id_str == ''){
			alert('请选择要添加的选项');
			return false;
		}
	}
	$.get(requestUrl,
	{act:act,essay_id:essay_id,essay_id_str:essay_id_str,class_code:class_code,speaker_number:speaker_number},
	function(data){
		alert(data);
		if(act=='delete'){
			window.location.reload();
		}
	}
	);
}


function testMessageBox_show_essayImg(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe src='"+requestUrl+"' width=\"1000\" height=\"800\"></iframe></div>";
	showMessageBox('查看作文图片',messContent,objPos,1050,0);
}


function add_student(class_code,speaker_number,requestUrl){
	if(class_code == '' || speaker_number == ''){
		$('#add_student_msg').val('非法操作');
		return false;
	}
	var student_name = $('#new_student').val();
	if(student_name ==''){
		$('#add_student_msg').val('请先填写学生姓名');
		return false;
	}
	$.post(requestUrl,
	{class_code:class_code,speaker_number:speaker_number,student_name:student_name},
	function(data){
		var obj = eval('(' + data + ')');
		$('#add_student_msg').html('');
		if(obj.status == 1){
			$('.Students').append("<li onclick=\"selectType('student','"+obj.student_code+'|'+student_name+"');$(this).addClass('bgcolor');\">"+student_name+"</li>");
		}else{
			$('#add_student_msg').html(obj.msg);
		}
	}
	);
}


function show_students(class_code,speaker_number,requestUrl){
	if(class_code == '' || speaker_number == ''){
		alert('非法操作');
		return false;
	}
	$(".Nums span").each(function(i,val){
		$(this).removeClass('bgcolor');
	});
	$.get(requestUrl,
	{class_code:class_code,speaker_number:speaker_number,class_info:$("#classInfo").val()},
	function(data){
		$('.Students').html(data);
	}
	);
}


function testMessageBox_changeAvatar(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:500px;\"><iframe src='"+requestUrl+"' width=\"570\" height=\"500\" style=\"border:0;\"></iframe></div>";
	showMessageBox('上传/修改头像',messContent,objPos,600,1);
}


function change_avatar(student_code,act,requestUrl){
	if(student_code != '' && act !='' && requestUrl!=''){
		$.post(requestUrl,
		{student_code:student_code,act:act,avatar:$("#avatar").val()},
		function(data){
			var obj = eval('(' + data + ')');
			if(obj.status==1){
				closeWindow(1);
				location.reload();
			}
		}
		);
	}else{
		$('#avatar_msg').html('非法操作');
	}
}


/*删除头像*/
function del_img_avatar(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			var obj = eval('(' + data + ')');
			alert(obj.status);
			if(obj.status==1){
				$("#avatar").val('');
				$("#pre_avatar").html('');
			}else{
				alert('删除失败');
			}
		}
	});
}