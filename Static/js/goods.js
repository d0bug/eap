$(function () {
	$("#checkAll").click(function () { //":checked"匹配所有的复选框
		if(this.checked){
			$("input[name='is_delete[]']:checkbox").attr("checked", true); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}else{
			$("input[name='is_delete[]']:checkbox").attr("checked", false); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}
	});
});


function set_mobile(requestUrl){
	if($('#user_mobile').val() != ''){
		var reg = /^(134|135|136|137|138|139|133|153|150|151|152|157|158|159|187|188|130|131|132|155|156|180|189|185|186|182)\d{8}$/;
		if(!reg.test($('#user_mobile').val())){
			$('#user_mobile_msg').html('手机号码格式错误');
		}else{
			$.get(requestUrl,
			{phone:$('#user_mobile').val()},
			function(data){
				if(data == 1){
					alert('手机号码设置成功');
					window.location.reload(); 
				}else{
					$('#user_mobile_msg').html('手机号码设置失败');
				}
			}
			);
		}
	}else{
		$('#user_mobile_msg').html('手机号码不能为空');
	}
}

function check_newphone(){
	if($("#newphone").val() == ''){
		$('#newphone_msg').html('请输入新手机号码');
		return false;
	}
	var reg = /^(134|135|136|137|138|139|133|153|150|151|152|157|158|159|187|188|130|131|132|155|156|180|189|185|186|182)\d{8}$/;
	if(!reg.test($("#newphone").val())){
		$('#newphone_msg').html('新手机号码格式错误');
		return false;
	}
}



//检查文字长度
function check_length(inputid, result, length){
	var strVal=$("#"+inputid).val();
	var strLen=$("#"+inputid).val().length;
	var strMsg = '';
	if (strLen <= length){
		//strMsg += "已经输入"+(strLen)+"个字，";
		strMsg += "还可以输入"+(length-strLen)+"个字";
		$("#"+result).html(strMsg);
	}else{
		$("#"+inputid).val($("#"+inputid).val().substring(0, length));
	}
	return true;
}


function check_editor(){
	var editorContent = $(document.getElementsByTagName('iframe')[0].contentWindow.document.body).html();
	if(editorContent == '<br>'){
		$('.error').html('请填写资讯内容');
		return false;
	}
}

function get_option(value,type,url,position){
	if(type!=''){
		if(position == 'list'){
			var sid = $("#subject").val();
		}else if(position == 'attr'){
			var sid = $("#sid").val();
		}
		if(type=='grade' || type=='gid'){
			$.get(url,
			{sid:value,return_type:'select', _tm:(new Date()).getTime()},
			function(data){
				$("#"+type).html(data);
			}
			);
		}else if(type=='knowledge' || type=='kid'){
			$.get(url,
			{gid:value,sid:sid,type:'list', _tm:(new Date()).getTime()},
			function(data){
				$("#"+type).html(data);
			}
			);
		}
	}
}


//讲义详细信息
function testMessageBox_handouts_detail(ev,hid,url){
	var objPos = mousePosition(ev);
	if(hid != '' && url !=''){
		$.get(url,
		{hid:hid},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:500px;\">"+data+"</div>";
			showMessageBox('校区分配',messContent,objPos,800);
		}
		);
	}
}



function testMessageBox_uploadOrDownloadList(ev,userKey,url,realname,type,start,end,getType){
	var objPos = mousePosition(ev);
	if(userKey != '' && url !=''){
		$.get(url,
		{userKey:userKey,type:type,startTime:start,endTime:end,getType:getType},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:300px;\">"+data+"</div>";
			if(getType == 'upload'){
				var title = '讲义上传统计';
			}else{
				var title = '讲义下载统计';
			}
			showMessageBox(realname+title,messContent,objPos,800);
		}
		);
	}
}


function testMessageBox_addTeacher(ev,teacherType){
	var objPos = mousePosition(ev);
	if(teacherType == 1){
		var title = '添加高思教师';
	}else{
		var title = '添加社会兼职教师';
	}
	messContent="<div class=\"mesWindowsBox\" style=\"height:300px;\">5555555</div>";
	showMessageBox(title,messContent,objPos,600);
}

