
$(function () {
	$("#checkAll").click(function () {
		if(this.checked){
			$("input[name='is_delete[]']:checkbox").attr("checked", true);
		}else{
			$("input[name='is_delete[]']:checkbox").attr("checked", false);
		}
	});
});


function checkAll(id){
	if($("#"+id).attr("checked")=='checked'){
		$("input[name='sid[]']:checkbox").attr("checked", true);
	}else{
		$("input[name='sid[]']:checkbox").attr("checked", false);
	}
}


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
function check_length(inputid, result, min_length){
	var strVal=$("#"+inputid).val();
	var strLen=$("#"+inputid).val().length;
	var strMsg = '';
	if (strLen <= min_length){
		//strMsg += "已经输入"+(strLen)+"个字，";
		strMsg += "课堂评价字数最低"+min_length+"字，还需要输入"+(min_length-strLen)+"个字";
		$("#"+result).html(strMsg);
	}else{
		//$("#"+inputid).val($("#"+inputid).val().substring(0, length));
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


function get_grades_option(sid,requestUrl){
	if(sid != ''){
		$.get(requestUrl,
		{sid:sid,return_type:'select', _tm:(new Date()).getTime()},
		function(data){
			$("#grade").html(data);
		}
		);
	}
	$('#subject_msg').html('');
}

function get_knowledge_options(gid,requestUrl){
	if(gid != ''){
		$.get(requestUrl,
		{gid:$("#course_id_two").val(),sid:$("#course_id_one").val(),type:'add', _tm:(new Date()).getTime()},
		function(data){
			$("#course_id_three").html(data);
		}
		);
	}
	$('#grade_msg').html('');
}

function get_knowledge_option(gid,requestUrl){
	if(gid != ''){
		$.get(requestUrl,
		{gid:$("#grade").val(),sid:$("#subject").val(),type:'add', _tm:(new Date()).getTime()},
		function(data){
			$("#knowledge").html(data);
		}
		);
	}
	$('#grade_msg').html('');
}


function get_nianji_option(kid,divId,requestUrl){
	if(kid != ''){
		$.get(requestUrl,
		{gid:$("#grade").val(),sid:$("#subject").val(),kid:$("#knowledge").val(),type:'add', _tm:(new Date()).getTime()},
		function(data){
			$(divId).html(data);
		}
		);
	}
	$('#knowledge_msg').html('');
}


/*删除图片*/
function del_img(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			if(data==1){
				$(inputid).val('');
				$(divid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
}

function get_options(value,type,url,position){
	if(type!=''){
		if(position == 'list'){
			var sid = $("#subject").val();
		}else if(position == 'attr'){
			var sid = $("#sid").val();
		}
		if(type=='grade' || type=='sid')
		{
			$.get(url,
			{id:value,return_type:'select', _tm:(new Date()).getTime()},
			function(data){;
				$("#course_id_one").html(data);
			}
			);
		}else if(type=='course_id_one' || type=='sid'){
			$.get(url,
			{id:value,return_type:'select', _tm:(new Date()).getTime()},
			function(data){
				$("#knowledge_type").html(data);
			}
			);
		}else if(type=='knowledge_type' || type=='sid'){
			$.get(url,
			{id:value,subject_id:$("#course_id_one").val(),return_type:'select', _tm:(new Date()).getTime()},
			function(data){
				$("#course_id_two").html(data);
			}
			);
		}else if(type=='course_id_two' || type=='gid'){
			$.get(url,
			{sid:value,return_type:'select', _tm:(new Date()).getTime()},
			function(data){
				$("#"+type).html(data);
			}
			);
		}else if(type=='course_id_three' || type=='kid'){
			$.get(url,
			{gid:value,sid:sid,type:'list', _tm:(new Date()).getTime()},
			function(data){
				$("#"+type).html(data);
			}
			);
		}
	}
}

function get_option(value,type,url,position){
	if(type!=''){
		if(position == 'list'){
			var sid = $("#subject").val();
		}else if(position == 'attr'){
			var sid = $("#sid").val();
		}
		if(type=='subject' || type=='sid'){
			$.get(url,
			{type:value,return_type:'select', _tm:(new Date()).getTime()},
			function(data){
				var obj = eval('(' + data + ')');
				$("#"+type).html(obj.subjectHtml);
				$("#grade").html(obj.gradeHtml);
			}
			);
		}else if(type=='grade' || type=='gid'){
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


/*教师宣传信息预览*/
function preview(requestUrl){
	var arr = get_form_vars();
	$.post(requestUrl,
	{avatar:arr['avatar'],teacher_name:arr['teacher_name'],gender:arr['gender'],subject:arr['subject'],grade:arr['grade'],send_word:arr['send_word'],intro_img:arr['intro_img'],intro_content:arr['intro_content'],teach_img:arr['teach_img'],teach_content:arr['teach_content'],achievement_content:arr['achievement_content'],experience_img:arr['experience_img'],experience_content:arr['experience_content'],comment:arr['comment']},
	function(data){
		var obj = eval('(' + data + ')');
		$("#preview_url").val(obj.url);
		window.open(obj.show_url);
	}
	);

}


/*获取宣传信息数据*/
function get_form_vars(){
	var arr = new Array();
	arr['avatar'] = $("#avatar").val();
	arr['teacher_name'] = $("#teacher_name").val();
	arr['gender'] = $('input:radio[name="gender"]:checked').val();
	arr['rank'] = $('input:radio[name="rank"]:checked').val();
	//获取学科
	var subject_str = '';
	$("input[name='subject[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			subject_str += $(this).val()+","
		}
	})
	arr['subject'] = subject_str;
	//获取年级
	var grade_str = '';
	$("input[name='grade[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			grade_str += $(this).val()+","
		}
	})
	arr['grade'] = grade_str;
	//获取教师资质
	var education_str = '';
	$("input[name='education[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			education_str += $(this).val()+","
		}
	})
	arr['education'] = education_str;
	//获取授课风格
	var style_str = '';
	$("input[name='style[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			style_str += $(this).val()+","
		}
	})
	arr['style'] = style_str;
	//获取授课校区
	var school_str = '';
	$("input[name='school[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			school_str += $(this).val()+","
		}
	})
	arr['school'] = school_str;


	var reg=new RegExp("\n","g");
	var send_word = $("#send_word").val();
	var intro_content = $("#intro_content").val();
	var teach_content = $("#teach_content").val();
	var achievement_content = $("#achievement_content").val();
	var experience_content = $("#experience_content").val();
	var comment = $("#comment").val();
	arr['send_word'] = send_word.replace(reg,"<br>");
	arr['intro_img'] = $("#intro_img").val();
	arr['intro_content'] = intro_content.replace(reg,"<br>");
	arr['teach_img'] = $("#teach_img").val();
	arr['teach_content'] =  teach_content.replace(reg,"<br>");
	arr['achievement_content'] = achievement_content.replace(reg,"<br>");
	arr['experience_img'] = $("#experience_img").val();
	arr['experience_content'] = experience_content.replace(reg,"<br>");
	arr['comment'] = comment.replace(reg,"<br>");
	return arr;
}


//讲义详细信息
function testMessageBox_handouts_detail(ev,hid,url,is_modify){
	var objPos = mousePosition(ev);
	if(hid != '' && url !=''){
		$.get(url,
		{hid:hid,is_modify:is_modify},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:400px;\">"+data+"</div>";
			showMessageBox('讲义信息',messContent,objPos,600,0);
		}
		);
	}
}


function testMessageBox_uploadOrDownloadList(ev,userKey,url,realname,type,start,end,getType,is_teaching_and_research,sid,gid){
	var objPos = mousePosition(ev);
	if(userKey != '' && url !=''){
		$.get(url,
		{userKey:userKey,type:type,startTime:start,endTime:end,getType:getType,is_teaching_and_research:is_teaching_and_research,realname:realname,sid:sid,gid:gid},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:300px;\">"+data+"</div>";
			if(getType == 'upload'){
				var title = '讲义上传统计';
			}else{
				var title = '讲义下载统计';
			}
			showMessageBox(realname+title,messContent,objPos,800,0);
		}
		);
	}
}


function testMessageBox_addTeacher(ev,teacherType,requestUrl,doAddUrl){
	var objPos = mousePosition(ev);
	if(teacherType == 1){
		var title = '添加高思教师';
		var content = '<p class=\"h_27\">用户登录名：<input type=text id=username name=username >@gaosiedu.com&nbsp;&nbsp;<input type=button value="检索邮箱" onclick="checkThisUserIsExist('+teacherType+',\'#returnMsg\',\''+requestUrl+'\',\''+doAddUrl+'\')"></p>';
		content = content+"<p class=\"h_27\">　　　　　　<span id=returnMsg></span></p><div id=teacherInfo></div>";
	}else{
		var title = '添加社会兼职教师';
		var content = '<p class=\"h_27\">用户登录名：<input type=text id=username name=username >&nbsp;&nbsp;<input type=button value="检索用户名是否重复" onclick="checkThisUserIsExist('+teacherType+',\'#returnMsg\',\''+requestUrl+'\',\''+doAddUrl+'\')"></p>';
		content = content+"<p class=\"h_27\">　　　　　　<span id=returnMsg></span></p>";
		content = content+"<div id=teacherInfo>";
		content = content+"<p class=\"h_27\">用户姓名：<input type=text id=realname name=realname value=''><label id=realnameMsg ></label></p>";
		content = content+"<p class=\"h_27\">初始密码：<input type=text id=password name=password value='123456'><label id=passwdMsg ></label></p>";
		content = content+"<p class=\"h_27\">所属部门：无</p>";
		content = content+"<p class=\"h_27\">教师类型：<input type=radio name=teacherType value=1 checked>兼职</p>";
		content = content+"<p class=\"h_27\">教师身份：无</p>";
		content = content+"<p class=\"h_27\">账号启用状态：<input type=radio name=is_removed value=0 checked>启用 <input type=radio name=is_removed value=1 >禁用</p>";
		content = content+"<p class=\"h_27\"><input type=hidden name=user_key id=user_key value=''><input type=button id=\"add_teacher\" value=确认添加 onclick=\"doAddTeacher(2,'"+doAddUrl+"')\"></p>";
		content = content+"</div>";
	}
	messContent="<div class=\"mesWindowsBox\" style=\"height:300px;\">"+content+"</div>";
	showMessageBox(title,messContent,objPos,500,0);
}


var Reg_username = /^[A-Za-z0-9]{0,30}$/;
var Reg_realname = /^[\u4E00-\u9FA5a-zA-Z0-9]{0,30}$/;
function checkThisUserIsExist(teacherType,returnDid,requestUrl,doAddUrl){
	var username = $("#username").val();
	if(username!=''){
		if(Reg_username.test(username)){
			$.get(requestUrl,
			{uname:username,utype:teacherType},
			function(data){
				var obj = eval('(' + data + ')');
				if(obj.status == 1){
					if(teacherType == 1){
						var content = "<p>用户姓名："+obj.real_name+"<input type=hidden id=realname name=realname value='"+obj.real_name+"'><label id=realnameMsg></label></p>";
						content = content + "<p>所属部门："+obj.department+"<input type=hidden id=department name=department value='"+obj.department+"'></p>";
						content = content + "<p>教师类型：<input type=radio name=teacherType value=0 checked>全职 </p>";
						content = content + "<p>教师身份：<input type=radio name=teacherPower value=0 checked>校区教师 <input type=radio name=teacherPower value=1 >教研教师</p>";
						content = content + "<p>账号启用状态：<input type=radio name=is_removed value=0 checked>启用 <input type=radio name=is_removed value=1 >禁用</p>";
						content = content + "<p><input type=hidden name=user_key id=user_key value='"+obj.user_key+"'><input type=button value=确认添加 onclick=\"return doAddTeacher(1,'"+doAddUrl+"')\"></p>";
						$("#teacherInfo").html(content);
					}
					$(returnDid).html("");
				}
				$("#user_key").val(obj.user_key);
				$(returnDid).html(obj.msg);
			}
			);
		}else{
			$(returnDid).html('<font color=red>请填写指包含字母和数字的用户登录名，长度不能超过30字符</font>');
		}
	}else{
		$(returnDid).html('<font color=red>请填写用户登录名后再进行检索</font>');
	}
}


function doAddTeacher(teacherType,doAddUrl){
	$("#realnameMsg").html('');
	$("#passwdMsg").html('');
	if(confirm("确认要添加此用户吗?")){
		if($("#username").val()==''){$("#returnMsg").html('<font color=red>用户登录名不能为空</font>');return false;}else{$("#returnMsg").html('');}
		if($("#realname").val()==''){$("#realnameMsg").html('<font color=red>用户姓名不能为空</font>');return false;}else{$("#realnameMsg").html('');}
		if(!Reg_realname.test($("#realname").val())){$("#realnameMsg").html('<font color=red>请输入正确的用户姓名</font>');return false;}else{$("#realnameMsg").html('');}
		if(teacherType == 2 && $("#password").val()==''){$("#passwdMsg").html('<font color=red>初始密码不能为空</font>');return false;}else{$("#passwdMsg").html('');}
		if(teacherType == 2 && $("#password").val().length >30){$("#passwdMsg").html('<font color=red>初始密码长度不能超过15字符</font>');return false;}else{$("#passwdMsg").html('');}
		if(teacherType == 1){
			var dataStr = {tType:teacherType,username:$("#username").val(),realname:$("#realname").val(),type:$('input:radio[name="teacherType"]:checked').val(),power:$('input:radio[name="teacherPower"]:checked').val(),isRemoved:$('input:radio[name="is_removed"]:checked').val(),userKey:$("#user_key").val(),department:$("#department").val()};
		}else{
			var dataStr = {tType:teacherType,username:$("#username").val(),realname:$("#realname").val(),passwd:$("#password").val(),type:$('input:radio[name="teacherType"]:checked').val(),isRemoved:$('input:radio[name="is_removed"]:checked').val(),userKey:$("#user_key").val()};
		}
		$.post(doAddUrl,
		dataStr,
		function(data){
			var obj = eval('(' + data + ')');
			alert(obj.msg);
			if(obj.status == 1){
				window.location.href = obj.url;
			}
		}
		);
	}else{
		return false;
	}
}


function testMessageBox_editTeacher(ev,userKey,is_employee,requestUrl,doEditUrl,page){
	var objPos = mousePosition(ev);
	if(is_employee ==1){
		var title = '编辑高思教师';
	}else{
		var title = '编辑社会兼职教师';
	}
	if(userKey!=''){
		$.post(requestUrl,
		{userKey:userKey},
		function(data){
			var obj = eval('(' + data + ')');
			if(obj.is_employee ==1){
				obj.user_name = obj.user_name +"@gaosiedu.com";
				var teacherType = "<input type=radio name=teacherType value=0 checked=\"checked\">全职";
				if(obj.is_teaching_and_research == 1){
					var teacherIdentity = "<input type=radio name=teacherPower value=0 >校区教师 <input type=radio name=teacherPower value=1 checked>教研教师";
				}else{
					var teacherIdentity = "<input type=radio name=teacherPower value=0 checked>校区教师 <input type=radio name=teacherPower value=1 >教研教师";
				}
			}
			if(obj.is_teacher == 1){
				var teacherType = "<input type=radio name=teacherType value=1 checked=\"checked\">兼职";
				var teacherIdentity = '无';
			}
			if(obj.is_removed == 1){
				var teacherStatus = "<input type=radio name=is_removed value=0 >启用 <input type=radio name=is_removed value=1 checked>禁用";
			}else{
				var teacherStatus = "<input type=radio name=is_removed value=0 checked>启用 <input type=radio name=is_removed value=1 >禁用";
			}
			var content =  "<p class=\"h_27\">用户登录名："+obj.user_name+"</p>";
			content =  content + "<p class=\"h_27\">&nbsp;&nbsp;&nbsp;用户姓名：<input type=text id=realname name=realname value='"+obj.user_realname+"'><label id=realnameMsg></label></p>";
			if(obj.is_teacher == 1){
				content =  content + "<p>初始化密码：<input type=text id=password name=password value='123456'><label id=passwdMsg></label></p>";
			}
			content = content + "<p class=\"h_27\">&nbsp;&nbsp;&nbsp;所属部门："+obj.department+"</p>";
			content = content + "<p class=\"h_27\">&nbsp;&nbsp;&nbsp;教师类型："+teacherType+" </p>";
			content = content + "<p class=\"h_27\">&nbsp;&nbsp;&nbsp;教师身份："+teacherIdentity+"</p>";
			content = content + "<p class=\"h_27\">账号启用状态："+teacherStatus+"</p>";
			content = content + "<p class=\"h_27\"><input type=hidden name=user_key id=user_key value='"+obj.user_key+"'><input type=button value=确认修改 onclick=\"return doEditTeacher('"+is_employee+"','"+doEditUrl+"','"+page+"')\"></p>";
			messContent="<div class=\"mesWindowsBox\" style=\"height:300px;\">"+content+"</div>";
			showMessageBox(title,messContent,objPos,800,1);
		}
		);
	}else{
		alert('非法操作');
	}
}


function doEditTeacher(is_employee,doEditUrl,page){
	$("#realnameMsg").html('');
	$("#passwdMsg").html('');
	if($("#realname").val()==''){$("#realnameMsg").html('<font color=red>用户姓名不能为空</font>');return false;}
	if(!Reg_realname.test($("#realname").val())){$("#realnameMsg").html('<font color=red>请输入正确的用户姓名</font>');return false;}
	if(is_employee == 0 && $("#password").val()==''){$("#passwdMsg").html('<font color=red>初始密码不能为空</font>');return false;}
	if(is_employee == 0 && $("#password").val().length >30){$("#passwdMsg").html('<font color=red>初始密码长度不能超过15字符</font>');return false;}
	if(is_employee == 1){
		var dataStr = {is_employee:is_employee,realname:$("#realname").val(),power:$('input:radio[name="teacherPower"]:checked').val(),isRemoved:$('input:radio[name="is_removed"]:checked').val(),userKey:$("#user_key").val(),p:page};
	}else{
		var dataStr = {is_employee:is_employee,realname:$("#realname").val(),passwd:$("#password").val(),isRemoved:$('input:radio[name="is_removed"]:checked').val(),userKey:$("#user_key").val(),p:page};
	}
	$.post(doEditUrl,
	dataStr,
	function(data){
		var obj = eval('(' + data + ')');
		alert(obj.msg);
		if(obj.status == 1){
			var userKey = $("#user_key").val();
			//触发cms系统，删除教师宣传信息
			if($('input:radio[name="is_removed"]:checked').val()==1){
				var option = '禁用';
				var requestUrl = "http://www.gaosivip.com/gsvipadmin.php/Admin-Teacher-dApi-"+userKey+"";
			}else{
				var option = '启用';
				var requestUrl = "http://www.gaosivip.com/gsvipadmin.php/Admin-Teacher-api-"+userKey+"";
			}
			$.ajax({
				async:false,
				url: requestUrl,
				type: 'get',
				dataType: 'jsonp',
				jsonp: 'callback',
				data: '', //请求数据
				success: function (data2) {
					if(data2.error == false){
						alert('网络宣传信息'+option+'成功'+data2.message);
					}else{
						alert('网络宣传信息'+option+'失败，'+data2.message);
					}
				},
				complete: function(XMLHttpRequest, textStatus){},
				error: function(xhr){
					alert("请求出错(请检查相关度网络状况.)");
				}
			});
			//window.location.href = obj.url;
		}
	}
	);
}


function deleteTeacher(deleteUrl){
	var userKeyStr = '';
	$("input[name='is_delete[]']:input").each(function(){
		if($(this).attr("checked")){
			userKeyStr += $(this).val()+"_";
		}
	});
	if(userKeyStr!=''){
		if(confirm("确认要删除选中用户吗?")){
			$.post(deleteUrl,
			{userKeyStr:userKeyStr},
			function(data){
				var obj = eval('(' + data + ')');
				if(obj.status == 1){
					alert('删除成功');
					var option = '禁用';
					$("input[name='is_delete[]']:input").each(function(){
						if($(this).attr("checked")){
							var userKey = $(this).val();
							var requestUrl = "http://www.gaosivip.com/gsvipadmin.php/Admin-Teacher-dApi-"+userKey+"";
							$.ajax({
								async:false,
								url: requestUrl,
								type: 'get',
								dataType: 'jsonp',
								jsonp: 'callback',
								data: '', //请求数据
								success: function (data2) {
									var userInfo = userKey.split("-");
									if(data2.error == false){
										alert(userInfo[1]+'的网络宣传信息'+option+'成功'+data2.message);
									}else{
										alert(userInfo[1]+'的网络宣传信息'+option+'失败，'+data2.message);
									}
								},
								complete: function(XMLHttpRequest, textStatus){},
								error: function(xhr){
									alert("请求出错(请检查相关度网络状况.)");
								}
							});
						}
					});
					window.location.href = obj.url;
				}else{
					alert('删除失败');
				}
			}
			);
		}else{
			return false;
		}
	}else{
		alert('未选中任何账号，无法删除');
		return false;
	}
}


function testMessageBox_selectTeacher(ev,doSelectUrl){
	var objPos = mousePosition(ev);
	var content = "<form id=selectForm method=post action=\""+doSelectUrl+"\" onsubmit=\"return checkSelectForm()\"><div >查询类型：<select id=\"selectType\" name=\"selectType\" onchange=\"changeSelectDetail(this.value,'#selectDetail')\"><option value=\"1\">登录名</option><option value=\"4\">用户姓名</option><option value=\"2\">教师类型</option><option value=\"3\">教师身份</option></select></div><br>";
	content += "<div id=\"selectDetail\"><input type=\"text\" id=\"username\" name=\"username\" value=\"\" placeholder=\"请输入登录名\"> <input type=submit value=' 立即查询 ' class='btn'></div></form>";
	messContent="<div class=\"mesWindowsBox\" style=\"height:120px;padding:30px;\">"+content+"<br><label id=\"returnMsg\" class=error></label></div>";
	showMessageBox('查询教师',messContent,objPos,400,0);
}


function changeSelectDetail(type,divId){
	if(type==1){
		var content = "<input type=\"text\" id=\"username\" name=\"username\" value=\"\"> ";
	}else if(type==2){
		var content = "<input type=\"radio\" name=\"teacherType\" value=\"0\" checked>全职教师&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"teacherType\" value=\"1\">兼职教师<br><br>";
	}else if(type==4){
		var content = "<input type=\"text\" id=\"user_realname\" name=\"user_realname\" value=\"\"> ";
	}else if(type==3){
		var content = "<input type=\"radio\" name=\"teacherPower\" value=\"0\" checked>校区教师&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"teacherPower\" value=\"1\">教研教师<br><br>";
	}
	content +=  "<input type=submit value=' 立即查询 ' class='btn'>";
	$(divId).html(content);
}


function checkSelectForm(){
	if($("#selectType").val() == 1 && $("#username").val() == ''){
		$("#returnMsg").html('请输入用户登录名在搜索');
		return false;
	}
}


function testMessageBox_subjectAccredit(ev,requestUrl){
	var userKeyStr = '';
	$("input[name='is_delete[]']:input").each(function(){
		if($(this).attr("checked")){
			userKeyStr += $(this).val()+"_";
		}
	});
	if(userKeyStr != ''){
		var objPos = mousePosition(ev);
		$.post(requestUrl,
		{userKeyStr:userKeyStr},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><form id=accreditForm method=post action=\""+requestUrl+"\">"+data+"<br><input type=submit name=submit value=\"确认授权\" class='btn'></form></div>";
			showMessageBox('查询教师',messContent,objPos,550,0);
		}
		);
	}else{
		alert('未选中任何账号，无法进行科目授权');
		return false;
	}
}


function testMessageBox_vipUserInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	$.get(requestUrl,
	function(data){
		if(data !=''){
			var obj = eval('(' + data + ')');
			var content  = "<p><b>用户登录名</b>："+obj.user_name+"</p>";
			content += "<p><b>用户姓名</b>："+obj.user_realname+"</p>";
			content += "<p><b>所属部门</b>："+obj.department+"</p>";
			content += "<p><b>教师类型</b>："+obj.teacherType+"</p>";
			content += "<p><b>教师身份</b>："+obj.teacherPower+"</p>";
			content += "<p><b>拥有角色</b>："+obj.roles+"</p>";
			content += "<p><b>科目权限</b>："+obj.subjectAccredit+"</p>";
			content += "<p><b>账号启用状态</b>：";
			if(obj.is_removed == 1){
				content += "<font color=red>已禁用</font>";
			}else{
				content += "<font color=green>已启用</font>";
			}
			content += "</p>";
		}else{
			var content = "非法操作";
		}
		messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\">"+content+"<br><br></div>";
		showMessageBox('查看教师信息',messContent,objPos,600,0);
	}
	);
}


function getCheckBoxValue(name){
	alert(name);
	var obj=document.getElementsByName(name);
	var valueStr='';
	for(var i=0; i<obj.length; i++){
		if(obj[i].checked) valueStr+=obj[i].value+',';  //如果选中，将value添加到变量s中
	}
	alert(valueStr);
	return valueStr;
}


function deleteAttribute(type,ntype,requestUrl){
	var confirmMsg;
	if(type=='subject'){
		confirmMsg = '删除该科目后，相关联的课程属性及讲义属性同时也将自动删除！且该科目下的所有讲义将无法进行搜索！\n确认删除该科目吗？';
		var idStr = '';
		if($("input[name='subject']:checked").val()!=undefined){
			idStr += $("input[name=subject]:checked").val()+'_';
		}
		var dataStr = {type:type,ntype:ntype,idStr:idStr}
	}else if(type=='grade'){
		confirmMsg = '删除课程属性后，相关联的讲义属性同时也将自动删除！且该课程属性下的所有讲义将无法进行搜索！\n确认删除该课程属性吗？';
		var sid = '';
		if($("input[name='subject']:checked").val()!=undefined){
			sid = $("input[name=subject]:checked").val();
		}
		var idStr = '';
		$("input[name='grade[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				idStr += $(this).val()+"_"
			}
		})
		var dataStr = {type:type,ntype:ntype,sid:sid,idStr:idStr}
	}else if(type=='knowledge'){
		confirmMsg = '删除该知识点后，该知识点下的所有讲义将无法进行搜索！\n确认删除该知识点吗？';
		var sid = '';
		if($("input[name='subject']:checked").val()!=undefined){
			sid = $("input[name=subject]:checked").val();
		}

		var gidStr = '';
		$("input[name='grade[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				gidStr += $(this).val()+"_"
			}
		})
		var idStr = '';
		$("input[name='knowledge[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				idStr += $(this).val()+"_";
			}
		});
		var dataStr = {type:type,ntype:ntype,sid:sid,gidStr:gidStr,idStr:idStr}
	}
	if(!confirm(confirmMsg)){
		return false;
	}else{
		if(idStr == ''){
			alert('请选择要删除的选项');
			return false;
		}else{
			$.post(requestUrl,
			dataStr,
			function(data){
				var obj = eval('(' + data + ')');
				alert(obj.msg);
				if(obj.status == 1){
					window.location.href = obj.url;
				}
			}
			);
		}
	}
}


function testMessageBox_editAttribute(ev,type,requestUrl){
	var id = '';
	if(type == 'subject'){
		title = '科目';
		var count = 0;
		$("input[name='subject']:radio").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}else if(type == 'grade'){
		title = '课程属性';
		var count = 0;
		$("input[name='grade[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}else if(type == 'knowledge'){
		title = '讲义属性';
		var count = 0;
		$("input[name='knowledge[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}
	if(id == ''){
		alert('请选择要编辑'+title+'，一次只允许编辑一条记录');
		return false;
	}
	if(count > 1){
		alert('一次只能修改一条记录,请去除多余选择');
		return false;
	}else{
		var name = $("#"+type+"_"+id).attr('title');
		var objPos = mousePosition(ev);
		var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkEditAttributeName('"+title+"')\"><p><input type=text id=\"name\" name=\"name\" value=\""+name+"\"><label id=\"name_msg\"></label></p><br><p><input type=hidden name=\"type\" value=\""+type+"\"><input type=hidden name=\"id\" value=\""+id+"\"><input type=submit value=\"修改\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#name').val('');\"></p></form>";
		messContent="<div class=\"mesWindowsBox\" style=\"height:100px;\">"+content+"</div>";
		showMessageBox('编辑'+title+'名称',messContent,objPos,300,0);
	}
}


function checkEditAttributeName(title){
	if($("#name").val()==''){
		$("#name_msg").html('<font color=red>'+title+'名称不能为空</font>');
		return false;
	}else{
		return true;
	}
}


function testMessageBox_editHandouts(ev,hid,requestUrl,type){
	var objPos = mousePosition(ev);
	if(hid !=''){
		$.get(requestUrl,
		{hid:hid,type:type},
		function(data){
			messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\">"+data+"</div>";
			showMessageBox('讲义修改',messContent,objPos,780,1);
		}
		);
	}else{
		alert('非法操作');
	}
}



function testMessageBox_reviewHandouts(ev,hid,requestUrl,source_type,title,user_key){
	var objPos = mousePosition(ev);
	if(hid !=''){
		messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\"><br><div class=\"center\">请选择该讲义是否通过审核？</div><br><div class=\"center\"><input type=radio id=status name=status value=\"1\" checked>是　　　　　<input type=radio id=status name=status value=\"2\">否</div><br><div class=\"center\"><input type=button value=\"　确认　\"  onclick=\"reviewHandouts('"+hid+"','"+requestUrl+"','"+source_type+"','"+title+"','"+user_key+"')\"></div></div>";
		showMessageBox('讲义审核',messContent,objPos,370,0);
	}else{
		alert('非法操作');
	}
}


function reviewHandouts(hid,requestUrl,source_type,title,user_key){
	if(hid !=''){
		$.get(requestUrl,
		{hid:hid,status:$("input[name=status]:checked").val(),source_type:source_type,title:title,user_key:user_key,date:new Date().toTimeString()},
		function(data){
			if(data=='0'){
				alert('审核失败');
				window.location.reload();
			}else{
				alert('审核成功');
				window.location.reload();
			}
		}
		);
	}else{
		alert('非法操作');
	}
}
function testMessageBox_deleteHandouts(ev,hid,requestUrl,source_type,user_key,title,real_name){
	var objPos = mousePosition(ev);
	if(hid != ''){
		messContent="<div class=\"mesWindowsBox\" style=\"height:130px;\"><br><div class=\"center\">您确定要删除该讲义/试题吗？</div><br><div class=\"center\"><input type=button value=\"　确认　\"  onclick=\"deleteHandouts('"+hid+"','"+requestUrl+"','"+source_type+"','"+user_key+"','"+title+"','"+real_name+"')\"></div></div>";
		showMessageBox('删除讲义',messContent,objPos,370,0);
	}else{
		alert('非法操作');
	}
}
function deleteHandouts(hid,requestUrl,source_type,user_key,title,real_name){
	if(hid !=''){
		$.get(requestUrl,
		{hid:hid,source_type:source_type,user_key:user_key,title:title,real_name:real_name,date:new Date().toTimeString()},
		function(data){
			if(parseInt(data) == '1'){
				alert('删除成功');
				window.location.reload();
			}else{
				alert('删除失败');
				window.location.reload();
			}
		}
		);
	}else{
		alert('非法操作');
	}
}

function testMessageBox_view_pdf(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"760\" height=\"700\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('教师版讲义预览',messContent,objPos,780,0);
}


function add_item(type,ntype,requestUrl){
	if(type != ''){
		if(type == 'subject'){
			var dataStr = {type:type,ntype:ntype};
		}
		if(type == 'grade'){
			var sid = '';
			if($("input[name='subject']:checked").val()!=undefined){
				sid = $("input[name=subject]:checked").val();
			}
			var dataStr = {type:type,ntype:ntype,sid:sid};
		}
		if(type == 'knowledge'){
			var sid = '';
			if($("input[name='subject']:checked").val()!=undefined){
				sid = $("input[name=subject]:checked").val();
			}
			var gidStr = '';
			$("input[name='grade[]']:checkbox").each(function(){
				if($(this).attr("checked")){
					gidStr += $(this).val()+"_"
				}
			});
			var dataStr = {type:type,ntype:ntype,sid:sid,gidStr:gidStr};
		}
		$.get(requestUrl,
		dataStr,
		function(data){
			$("#popup").show().html(data);
		}
		);
	}
}


function check_add_item(type,ntype){
	var is_submit = true;
	if(type == 'subject'){
		var max_length = 30;
		var empty_msg = '请填写科目名称';
		var length_msg = '科目名称超过30个字符';
	}
	if(type == 'grade'){
		var subject_id = false;
		for(var i=0;i<document.add_item_form.subject_id.length;i++){
			if(document.add_item_form.subject_id[i].checked){
				subject_id=true;
			}
		}
		if(!subject_id){
			$('#subject_id_msg').html('请选择所属科目');
			is_submit = false;
		}else{
			$('#subject_id_msg').html('');
		}
		var max_length = 20;
		if(ntype == 0){
			var ntype_msg = '课程';
		}else{
			var ntype_msg = '题库';
		}
		var empty_msg = '请填写'+ntype_msg+'属性名称';
		var length_msg = ntype_msg+'属性名称超过20个字符';
	}
	if(type == 'knowledge'){
		var subject_id = false;
		for(var i=0;i<document.add_item_form.subject_id.length;i++){
			if(document.add_item_form.subject_id[i].checked){
				subject_id=true;
			}
		}
		if(!subject_id){
			$('#subject_id_msg').html('请选择所属科目');
			is_submit = false;
		}else{
			$('#subject_id_msg').html('');
		}
		var falg = 0;
		$("input[name=grade_id[]]:checkbox").each(function(){
			if($(this).attr("checked")){
				falg +=1;
			}
		});
		if(falg == 0){
			if(ntype == 0){
				var html= '请选择课程属性';
			}else{
				var html= '请选择题库属性';
			}
			$('#grade_id_msg').html(html);
			is_submit = false;
		}else{
			$('#grade_id_msg').html('');
		}
		var max_length = 20;
		if(ntype == 0){
			var ntype_msg = '讲义';
		}else{
			var ntype_msg = '试题';
		}
		var empty_msg = '请填写'+ntype_msg+'属性名称';
		var length_msg = ntype_msg+'属性名称超过20个字符';
	}
	if($("#name").val() == ''){
		$('#name_msg').html(empty_msg);
		is_submit = false;
	}else{
		$('#name_msg').html('');
	}
	if($("#name").val().length > max_length){
		$('#name_msg').html(length_msg);
		is_submit = false;
	}
	if(is_submit == false){
		return false;
	}
	return true;
}


function changeSubjectList(type,requestUrl){
	$.get(requestUrl,
	{type:type},
	function(data){
		var obj = eval('(' + data + ')');
		$("#subject").html(obj.subjectHtml);
		$("#grade").html(obj.gradeHtml);
		$("#knowledge").html(obj.knowledgeHtml);
	}
	);
}


function update_department(requestUrl){
	$.get(requestUrl,
	function(data){
		alert(data);
		location.reload();
	}
	);
}


function testMessageBox_handouts_preview(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"1000\" height=\"800\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('教师版讲义预览',messContent,objPos,1030,0);
}

function init_uploadify(divid,type){
	if(type == 'handouts'){
		$(divid).html('');
		$('#add_button').html('<input type="button" value="添加文档" onclick="add_document(\'#upload\')">');
		$('#document_num').val(0);
		$('#document_num_real').val(0);
	}
	if(type == 'itembank'){//试题库文档和教师上传文档
		var html = '<span id="upload_item_bank"></span><label id="upload_itembank_msg" class="success"></label><br><span id="view_teacher_file"></span><input type="hidden" id="teacher_version" name="teacher_version" value=""><input type="hidden" id="teacher_version_preview" name="teacher_version_preview" value="">';
		var js = "<script>$('#upload_item_bank').uploadify({"+
		"'auto'     : true,"+
		"'removeTimeout' : 1,"+
		"'swf'      : '/static/js/uploadify.swf',"+
		"'uploader' : $(\"#uploadimg_url\").val(),"+
		"'method'   : 'post',"+
		"'formData':{'preview':$(\"#is_preview\").val(),'width':'160','height':'100',type:'file','action':'$(\"#action\").val()','hid':$(\"#hid\").val(),'prename':$(\"#subject\").find(\"option:selected\").text()+''+$(\"#grade\").find(\"option:selected\").text()+'_'+$(\"#knowledge\").find(\"option:selected\").text(),'is_realname':1},"+
		"'buttonText' : '点击选择文档',"+
		"'width':'200',"+
		"'multi'    : false,"+
		"'fileTypeDesc' : 'pdf Files',"+
		"'fileTypeExts' : '*.doc;*.docx; *.pdf; *.ppt; *.xls',"+
		"'fileSizeLimit' : '10240KB',"+
		"'onUploadSuccess':function(file,data,response){"+
		"	var obj = eval('(' + data + ')');"+
		"	$('#upload_itembank_msg').html(obj.status);"+
		"	$('#teacher_version').val(obj.url);"+
		"	$('#teacher_version_preview').val(obj.preview_url);"+
		"	$('#view_teacher_file').html(\"<a href='#none'>\"+obj.show_url+\"</a>&nbsp;&nbsp;<a href='#none' onclick=del_img('\"+obj.url+\"','#view_teacher_file','#teacher_version','\"+$('#delimg_url').val()+\"')>删除</a>\");"+
		"}"+
		"});</script>";
		$(divid).html('');
		$(divid).append(html+js);
		}
}

function add_document(divid){
	if($('#subject').val()==''){
		$('#subject_msg').html('请先选择科目');
		alert('请先选择科目');
	}else if($('#grade').val()==''){
		$('#grade_msg').html('请先选择课程属性');
		alert('请先选择课程属性');
	}else if($('#knowledge').val()==''){
		$('#knowledge_msg').html('请先选择讲义属性');
		alert('请先选择讲义属性');
	}else{
		var num = Number($("#document_num").val());
		var new_num = num+1;
		var html = '<li id="'+new_num+'"><span id="upload_teacher_version_'+new_num+'"></span><label id="teacher_version_msg_'+new_num+'" class="success"></label><br>'+
		'<span id="view_teacher_file_'+new_num+'" class="view_file"></span><input type="hidden" id="teacher_version_'+new_num+'" name="teacher_version[]" value=""><input type="hidden" id="teacher_version_preview_'+new_num+'" name="teacher_version_preview[]" value="">'+
		'<span id="upload_student_version_'+new_num+'"></span><label id="student_version_msg_'+new_num+'" class="success"></label><br>'+
		'<span id="view_student_file_'+new_num+'" class="view_file"></span><input type="hidden" id="student_version_'+new_num+'" name="student_version[]" value=""><input type="hidden" id="student_version_preview_'+new_num+'" name="student_version_preview[]" value="">'+
		'<input type="hidden" id="student_version_realname_'+new_num+'" name="student_version_realname[]" value="">'+
		'<label id="upload_handouts_msg_'+new_num+'"></label><div class="t_right"><a href="#none" onclick="del_li(\''+new_num+'\')">删除</a></div></li>';
		var js = "<script>$('#upload_teacher_version_"+new_num+"').uploadify({"+
		"'auto'     : true,"+
		"'removeTimeout' : 1,"+
		"'swf'      : '/static/js/uploadify.swf',"+
		"'uploader' : $('#uploadimg_url').val(),"+
		"'method'   : 'post',"+
		"'formData':{'preview':1,'width':'160','height':'100',type:'file','action':'insert','hid':0,'prename':$(\"#subject\").find(\"option:selected\").text()+''+$(\"#grade\").find(\"option:selected\").text()+'_'+$(\"#knowledge\").find(\"option:selected\").text(),'is_realname':1},"+
		"'buttonText' : '点击选择教师版讲义',"+
		"'width':'200',"+
		"'multi'    : false,"+
		"'fileTypeDesc' : 'doc Files',"+
		"'fileTypeExts' : '*.pdf;*.ppt;*.pptx',"+
		"'fileSizeLimit' : '10240KB',"+
		"'onUploadSuccess':function(file,data,response){"+
		"	var obj = eval('(' + data + ')');"+
		"	$('#teacher_version_msg_"+new_num+"').html(obj.status);"+
		"	$('#teacher_version_"+new_num+"').val(obj.url);"+
		"	$('#teacher_version_preview_"+new_num+"').val(obj.preview_url);"+
		"	$('#view_teacher_file_"+new_num+"').html(\"<a href='#none'>\"+obj.show_url+\"</a>&nbsp;&nbsp;<a href='#none' onclick=del_img('\"+obj.url+\"','#view_teacher_file_"+new_num+"','#teacher_version_"+new_num+"','\"+$('#delimg_url').val()+\"')>删除</a>\");"+
		"}"+
		"});"+

		//学生版讲义
		"$('#upload_student_version_"+new_num+"').uploadify({"+
		"'auto'     : true,"+
		"'removeTimeout' : 1,"+
		"'swf'      : '/static/js/uploadify.swf',"+
		"'uploader' : $('#uploadimg_url').val(),"+
		"'method'   : 'post',"+
		"'formData':{'preview':1,'width':'160','height':'100',type:'file','action':'insert','hid':0,'prename':$(\"#subject\").find(\"option:selected\").text()+''+$(\"#grade\").find(\"option:selected\").text()+'_'+$(\"#knowledge\").find(\"option:selected\").text(),'is_realname':1},"+
		"'buttonText' : '点击选择学生版讲义',"+
		"'width':'200',"+
		"'multi'    : false,"+
		"'fileTypeDesc' : 'pdf Files',"+
		"'fileTypeExts' : '*.doc; *.docx;*.ppt;*.pptx ',"+
		"'fileSizeLimit' : '10240KB',"+
		"'onUploadSuccess':function(file,data,response){"+
		"	var obj = eval('(' + data + ')');"+
		"	$('#student_version_msg_"+new_num+"').html(obj.status);"+
		"	$('#student_version_realname_"+new_num+"').val(obj.realname);"+
		"	$('#student_version_"+new_num+"').val(obj.url);"+
		"	$('#student_version_preview_"+new_num+"').val(obj.preview_url);"+
		"	$('#view_student_file_"+new_num+"').html(\"<a href='#none'>\"+obj.show_url+\"</a>&nbsp;&nbsp;<a href='#none' onclick=del_img('\"+obj.url+\"','#view_student_file_"+new_num+"','#student_version_"+new_num+"','\"+$('#delimg_url').val()+\"')>删除</a>\");"+
		"}"+
		"});</script>";
		$(divid).append(html+js);
		$("#document_num").val(new_num);
		$("#document_num_real").val(new_num);
		$('#document_msg').html('');
	}

}


function del_li(num){
	$("#"+num).remove();
	$("#document_num_real").val(Number($("#document_num").val())-1);
}


function check_add_handouts(){
	if($('#action').val()=='update' && $('#title').val()==''){
		$('#title_msg').html('请填写讲义标题');
		return false;
	}
	if($('#subject').val()==''){
		$('#subject_msg').html('请选择科目');
		return false;
	}
	if($('#grade').val()==''){
		$('#grade_msg').html('请选择课程属性');
		return false;
	}
	if($('#knowledge').val()==''){
		$('#knowledge_msg').html('请选择讲义属性');
		return false;
	}
	if($("#document_num_real").val()<=0){
		$('#document_msg').html('请上传讲义');
		return false;
	}
	var num = Number($('#document_num').val());
	var real_teacher = 0;
	var real_student = 0;
	for(var i=0;i<=num;i++){
		if($("#teacher_version_"+i).length){
			if($("#teacher_version_"+i).val()==''){
				$("#teacher_version_msg_"+i).removeClass('success');
				$("#teacher_version_msg_"+i).addClass('error');
				$("#teacher_version_msg_"+i).attr("display","block");
				$("#teacher_version_msg_"+i).html('请添加教师版讲义');
			}else{
				real_teacher++;
			}
			if($("#student_version_"+i).val()==''){
				$("#student_version_msg_"+i).removeClass('success');
				$("#student_version_msg_"+i).addClass('error');
				$("#student_version_msg_"+i).attr("display","block");
				$("#student_version_msg_"+i).html('请添加学生版讲义');
			}else{
				real_student++
			}
		}
	}
	if(real_student != $("#document_num_real").val() || real_teacher!= $("#document_num_real").val()){
		return false;
	}else{
		return true;
	}
}


function testMessageBox_handouts_helu(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"680\" height=\"550\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('核录课时',messContent,objPos,700,1);
}



function do_absence(requestUrl,helu_id){
	if(confirm('确定要标记缺勤吗？')){
		$.get(requestUrl,
		{helu_id:helu_id},
		function(data){
			alert(data);
			location.reload();
		}
		);
	}
}

function add_trainingProgram(code,name,requestUrl){
	if($('#kecheng_code').val()==''){
		alert('请选择课程');
		return false;
	}else{
		if($("#teacher_version").val()==''){
			alert('请选择要上传的培养方案文档');
			return false;
		}else{
			$.post(requestUrl,
			{url:$("#teacher_version").val(),student_code:code,student_name:name,kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_code option:selected').text(),teacher_code:$('#teacher_code').val(),teacher_name:$('#teacher_name').val()},
			function(data){
				var obj = eval('(' + data + ')');
				alert(obj.msg);
				if(obj.status == 1){
					$('#programList').html(obj.html);
					location.reload();
				}
				$('#upload_itembank_msg').html('');
				$('#teacher_version').val('');
				$('#view_teacher_file').html('');
			}
			);
		}
	}

}

function del_program(did,requestUrl){
	if(confirm('确定要删除该培养方案吗？')){
		$.get(requestUrl,
		function(data){
			if(data == 1){
				alert('删除成功');
				$(did).remove();
			}else{
				alert('删除失败');
			}
		}
		);
	}
}


function adjust_kecheng(requestUrl){
	if($("#start").val()!='' && $("#end").val()!=''){
		if(confirm('确定要调整课次时间吗？')){
			$.post(requestUrl,
			{helu_id:$("#helu_id").val(),start:$("#start").val(),end:$("#end").val()},
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


function add_kecheng(requestUrl){
	if($("#student_code").val()!='' && $("#kecheng_code").val()!='' && $("#start").val()!='' && $("#end").val()!=''){
		if(confirm('确定要加课吗？')){
			$.post(requestUrl,
			{student_code:$("#student_code").val(),kecheng_code:$("#kecheng_code").val(),start:$("#start").val(),end:$("#end").val()},
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
		$("#add_msg").html('请选择学员、课程、上课时间');
	}
}


function format_timeNumber(timeNumber){
	if(timeNumber<10){
		timeNumber = '0'+timeNumber;
	}
	return timeNumber;
}


function get_teacherList(deptCode,requestUrl){
	$.get(requestUrl,
	{deptCode:deptCode},
	function(data){
		$('#teacherCode').html(data);
	}
	);
}


function get_studentList(teacherCode,requestUrl){
	$.get(requestUrl,
	{teacherCode:teacherCode},
	function(data){
		$('#studentCode').html(data);
	}
	);
}


function get_kechengList(stuCode,teacherCode,requestUrl){
	if(stuCode!='' && teacherCode!=''){
		$.get(requestUrl,
		{stuCode:stuCode,teacherCode:teacherCode},
		function(data){
			$('#kechengHtml').html(data);
		}
		);
	}
}


function checkStyleNum(checkboxId){
	var flag = 0;
	$("input[name='style[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			flag +=1;
		}
	});
	if(flag >=3){
		alert('最多只能选择两个教师风格！');
		$('#'+checkboxId).removeAttr("checked");
	}
}

//合并表格中相同的部分
function _w_table_rowspan(_w_table_id,_w_table_colnum){
	_w_table_firsttd = "";
	_w_table_currenttd = "";
	_w_table_SpanNum = 0;
	_w_table_Obj = $(_w_table_id + " tr td:nth-child(" + _w_table_colnum + ")");
	_w_table_Obj.each(function(i){
		if(i==0){
			_w_table_firsttd = $(this);
			_w_table_SpanNum = 1;
		}else{
			_w_table_currenttd = $(this);
			if(_w_table_firsttd.text()==_w_table_currenttd.text()){
				_w_table_SpanNum++;
				_w_table_currenttd.hide(); //remove();
				_w_table_firsttd.attr("rowSpan",_w_table_SpanNum);
			}else{
				_w_table_firsttd = $(this);
				_w_table_SpanNum = 1;
			}
		}
	});
}
$(function(){
	$("textarea[name='course_user']").focus(function(){
		var msg = $(this).val();
		if(msg == '请在此输入课程的用途、使用时间、针对学生类型等……'){
			$(this).val("");
		}
	})
	$("textarea[name='course_user']").blur(function(){
		var msg = $(this).val();
		if(msg == ''){
			$(this).val("请在此输入课程的用途、使用时间、针对学生类型等……");
		}
	})

	_w_table_rowspan("#courseJiangYiTable",1);
	_w_table_rowspan("#courseJiangYiTable",2);
	_w_table_rowspan("#courseJiangYiTable",3);
	_w_table_rowspan("#courseJiangYiTable",4);
	_w_table_rowspan("#shiTiKuTable",1);
	_w_table_rowspan("#shiTiKuTable",2);
	_w_table_rowspan("#shiTiKuTable",3);
	_w_table_rowspan("#shiTiKuTable",4);

	_w_table_rowspan("#courseJiangYi",1);
	_w_table_rowspan("#courseJiangYi",2);
	_w_table_rowspan("#shiTiKu",1);
	_w_table_rowspan("#shiTiKu",2);

})

function hideDeleteMsg(msgId,requestUrl){
	$.get(requestUrl,function(data){
		if(parseInt(data) == 1){
			$("#msgConfirmLi_"+msgId).hide();
		}
	})
}

function testMessageBox_view_file(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"760\" height=\"700\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('文件预览',messContent,objPos,780,0);
}


function do_overdue(requestUrl){
	$.post(requestUrl,
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.status == 1){
			alert('超过48小时的课次不能进行核录');
			location.reload();
		}
	}
	);
}


function change_dimesion(sid,requestUrl){
	if(sid == ''){
		alert('非法操作');
		return false;
	}
	$.get(requestUrl,
	{sid:sid},
	function(data){
		var obj = eval('(' + data + ')');
		$('#dimesionHtml').html(obj.html);
		$('#commentHtml').html('');
	}
	);
}


function change_comment_text(level_id,requestUrl){

	var	sid = $("input[name=sid]:checked").val();
	var	dimension_id = $("input[name=dimension_id]:checked").val();

	if(sid == 'undefined'){
		alert('请选择科目');
		return false;
	}
	if(dimension_id == 'undefined'){
		alert('请选择维度');
		return false;
	}
	if(level_id == ''){
		alert('请选择级别');
		return false;
	}
	$.get(requestUrl,
	{sid:sid,dimension_id:dimension_id,level_id:level_id},
	function(data){
		var obj = eval('(' + data + ')');
		$('#commentHtml').html(obj.html);
	}
	);
}



function delete_comment(requestUrl){
	var comment_id_str = '';
	$("input[name='comment_id[]']:checkbox").each(function(){
		if($(this).attr("checked")){
			comment_id_str += $(this).val()+","
		}
	})
	if(comment_id_str==''){
		alert('请选择要删除的评语');
		return false;
	}
	var	sid = $("input[name=sid]:checked").val();
	var	dimension_id = $("input[name=dimension_id]:checked").val();
	var	level_id = $("input[name=level_id]:checked").val();
	$.post(requestUrl,
	{comment_id_str:comment_id_str,sid:sid,dimension_id:dimension_id,level_id:level_id},
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.status == 1){
			alert('删除成功');
			$('#commentHtml').html(obj.html);
		}else{
			alert('删除失败');
		}

	}
	);
}


function clear_level(){
	$("input[name=level_id]").each(function(){
		if(this.checked){
			$("input[name='level_id']:radio").attr("checked", false);
		}
	});

}


function testMessageBox_add_comment(ev,requestUrl){
	var	sid = $("input[name=sid]:checked").val();
	var	dimension_id = $("input[name=dimension_id]:checked").val();
	var	level_id = $("input[name=level_id]:checked").val();
	if(sid == '' || sid == undefined || dimension_id == '' || dimension_id == undefined || level_id == '' || level_id == undefined ){
		alert('请先选择科目、维度、级别等选项');
		return false;
	}else{
		var objPos = mousePosition(ev);
		messContent="<div class=\"mesWindowsBox\" style=\"height:100px;\"><p><input type=\"text\" id=\"text\" name=\"text\" value=\"\" placeholder=\"请输入评语\"></p><br><p><input type=button value=\"提交\" onclick=\"add_commentText("+sid+","+dimension_id+","+level_id+",'"+requestUrl+"')\" class=\"btn\"></p></div>";
		showMessageBox('添加评语',messContent,objPos,480,1);
	}

}


function add_commentText(sid,dimension_id,level_id,requestUrl){
	var text = $('#text').val();
	if(text == ''){
		alert('请填写要添加的评语');
		return false;
	}
	$.post(requestUrl,
	{text:text,sid:sid,dimension_id:dimension_id,level_id:level_id},
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.status == 1){
			alert('添加成功');
			$('#commentHtml').html(obj.html);
			$("#mesWindow").hide();
			$("#back").hide();
		}else{
			alert('添加失败');
		}
	}
	);
}



function testMessageBox_editCommentType(ev,type,requestUrl){
	var id = '';
	if(type == 'dimension'){
		title = '维度';
		var count = 0;
		$("input[name='dimension_id']:radio").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}else if(type == 'level'){
		title = '级别';
		var count = 0;
		$("input[name='level_id']:radio").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}
	if(id == ''){
		alert('请选择要编辑的'+title+'，一次只允许编辑一条记录');
		return false;
	}
	if(count > 1){
		alert('一次只能修改一条记录,请去除多余选择');
		return false;
	}else{
		var name = $("#"+type+"_id"+id).attr('title');
		var objPos = mousePosition(ev);
		var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkEditCommentType('"+title+"','"+type+"')\"><p>名称：<input type=text id=\"name\" name=\"name\" value=\""+name+"\"><label id=\"name_msg\"></label></p>";
		if(type == 'level'){
			content += "<br><p>等级：<input type=text id=\"rank\" name=\"rank\" value=\""+$("#"+type+"_id"+id).attr('alt')+"\"><label id=\"rank_msg\"></label></p>";
		}
		content += "<br><p><input type=hidden name=\"type\" value=\""+type+"\"><input type=hidden name=\"id\" value=\""+id+"\"><input type=submit value=\"修改\" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#name').val('');\" class=\"btn\"></p></form>"; 
		messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\">"+content+"</div>";
		showMessageBox('编辑'+title+'名称',messContent,objPos,400,0);
	}
}


function checkEditCommentType(title,type){
	var name = $("#name").val();
	if(name == ''){
		$("#name_msg").html('<font color=red>'+title+'名称不能为空</font>');
		return false;
	}
	if(name.length > 10){
		$("#name_msg").html('<font color=red>'+title+'名称长度不能超过10个字符</font>');
		return false;
	}
	if(type == 'level'){
		if($('#rank').val() == ''){
			$("#rank_msg").html('<font color=red>等级不能为空</font>');
			return false;
		}
		if(isNaN($('#rank').val())){
			$("#rank_msg").html('<font color=red>等级必须为数字</font>');
			return false;
		}
	}
	var reg = /^[\u4e00-\u9fa5a-z0-9]+$/gi;//只能输入汉字、英文字母、数字
	if(!reg.test(name)){
		$("#name_msg").html('<font color=red>'+title+'名称不能包含特殊字符</font>');
		return false;
	}
	return true;
	
}


function show_template(sid,requestUrl){
	if(sid == ''){
		alert('请先选择科目');
		return false;
	}
	$.get(requestUrl,
	{sid:sid},
	function(data){
		var obj = eval('(' + data + ')');
		$('#templateHtml').html(obj.html);
	}
	);
}



function testMessageBox_viewCommentTemplate(ev,requestUrl){
	$.get(requestUrl,
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.text == ''){
			var content = '课堂评价（话术）预览内容获取失败';
		}else{
			var content = obj.text;
		}
		var objPos = mousePosition(ev);
		messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\">"+content+"</div>";
		showMessageBox('预览课堂评价（话术）',messContent,objPos,400,0);
	}
	);

}


function testMessageBox_editCommentTemplate(ev,requestUrl,text){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:250px;\"><form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkCommentTemplate()\"><p><br><textarea id=\"text\" name=\"text\" cols=\"80\" rows=\"3\">"+text+"</textarea><br><label id=\"text_msg\"></label></p><br><p><input type=submit value=\" 修改 \" class=\"btn\"></p></form></div>";
	showMessageBox('编辑课堂评价（话术）',messContent,objPos,600,0);
}


function testMessageBox_add_commentTemplate(ev,requestUrl){
	var	sid = $("input[name=sid]:checked").val();
	if(sid == ''){
		alert('请先选择科目');
		return false;
	}else{
		var objPos = mousePosition(ev);
		var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkCommentTemplate()\"><p><br><textarea id=\"text\" name=\"text\" cols=\"80\" rows=\"3\"></textarea><label id=\"text_msg\"></label></p><br><p><input type=hidden name=\"sid\" id=\"sid\" value=\""+sid+"\"><input type=submit value=\" 添加 \" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#text').val('');\" class=\"btn\"></p></form>";
		messContent="<div class=\"mesWindowsBox\" style=\"height:250px;\">"+content+"</div>";
		showMessageBox('添加课堂评价（话术）',messContent,objPos,550,0);
	}

}


function checkCommentTemplate(){
	var text = $("#text").val();
	if(text==''){
		$("#text_msg").html('<font color=red>话术内容不能为空</font>');
		return false;
	}
	return true;
	
}


function delete_error_question(eid,requestUrl){
	$.post(requestUrl,
	{eid:eid},
	function(data){
		var obj = eval('(' + data + ')');
		alert(obj.msg);
		if(obj.status == 1){
			location.reload();
		}
	}
	);
}


//打印页面部分内容
function print_preview(oper) {
	if (oper < 10){
		bdhtml=window.document.body.innerHTML;//获取当前页的html代码
		sprnstr="<!--startprint"+oper+"-->";//设置打印开始区域
		eprnstr="<!--endprint"+oper+"-->";//设置打印结束区域
		prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)+18); //从开始代码向后取html

		prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));//从结束代码向前取html
		window.document.body.innerHTML=prnhtml;
		window.print();
		window.document.body.innerHTML=bdhtml;

	} else{
		window.print();
	}

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
	}
	//alert('例题答题情况：'+module_answer);

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
	}
	//alert('随堂练习答题情况：'+practise_answer);


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
		data:{helu_id:$('#helu_id').val(),student_code:$('#student_code').val(),student_name:$('#student_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:$('#lesson_no').val(),lesson_date:$('#lesson_date').val(),lesson_begin:$('#lesson_begin').val(),lesson_end:$('#lesson_end').val(),lesson_topic:$('#lesson_topic').val(),module_answer:module_answer,practise_answer:practise_answer,lastwork_answer:lastwork_answer,last_helu_id:$('#last_helu_id').val()},
		beforeSend: function(data){
			handleblockUI();
		},
		success:function(data){
			handleunblockUI();
			var obj = eval('(' + data + ')');
			alert(obj.msg);
		}
	});

}



function save_form2(requestUrl){
	if($('#lesson_topic').val() == ''){
		alert('请先将课次主题及课堂掌握情况进行保存');
		return false;
	}
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
	var is_send_sms = 0;
	if($("#is_send_sms").attr("checked")){
		is_send_sms = 1;
	}
	var reg=new RegExp("\n","g");
	var comment = $('#comment').val().replace(reg,"<br>");
	if(dimension_id_str != '' && dimension_title_str != '' && level_str != '' && $('#comment').val()!=''){
		if($('#comment').val().length<70){
			alert('课堂评价字数不能少于70字');
			return false;
		}else{
			$.ajax({
				type: 'POST',
				url: requestUrl,
				data:{act:$('#act').val(),helu_id:$('#helu_id').val(),student_code:$('#student_code').val(),student_name:$('#student_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:$('#lesson_no').val(),lesson_date:$('#lesson_date').val(),lesson_begin:$('#lesson_begin').val(),lesson_end:$('#lesson_end').val(),lesson_topic:$('#lesson_topic').val(),dimension_id_str:dimension_id_str,dimension_title_str:dimension_title_str,level_str:level_str,comment:comment,lesson_topic:$('#lesson_topic').val(),is_send_sms:is_send_sms},
				beforeSend: function(data){
					handleblockUI();
				},
				success:function(data){
					handleunblockUI();
					var obj = eval('(' + data + ')');
					alert(obj.msg);
				}
			});
		}
		
	}else{
		alert('请将课堂评价填写完整');
		return false;
	}
}


function del_recordimg(url,li_id,helu_id,requestUrl){
	var num = $('#record_count').val();
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url,helu_id:helu_id},
		success:function(data){
			if(data==1){
				alert('删除成功');
				//location.reload();
				$('#record_count').val(parseInt(num)-1);
				$(li_id).remove();
			}else{
				alert('删除失败');
			}
		}
	});
}



function createLessonReport(helu_id,requestUrl){
	//if(confirm("请先确认是否已将课堂掌握情况和课节评价进行保存？")){
 		var lesson_topic = $('#lesson_topic').val();
		if(lesson_topic==''){
			alert('课次主题不能为空');
			return false;
		}
		var record_count = $('#record_count').val();
		//if(record_count>0 && record_count < 3){
		//	alert('请按照要求上传至少3张轨照');
		//	return false;
		//}
		$.ajax({
			type: 'POST',
			url: requestUrl,
			data:{helu_id:helu_id},
			beforeSend: function(data){
				handleblockUI();
			},
			success:function(data){
				handleunblockUI();
				var obj = eval('(' + data + ')');
				if(obj.status==1){
					alert('学习报告生成成功');
					window.location.href = obj.report_url+"?time="+(new Date()).getTime();
				}else{
					alert('学习报告生成失败');
				}
			}
		});
 	//}
	

}




function testMessageBox_addCommentType(ev,type,requestUrl){
	var sid = '';
	$("input[name='sid']:radio").each(function(){
		if($(this).attr("checked")){
			sid = $(this).val();
			sname = $(this).attr('title');
		}
	});
	if(sid == '' || sid == undefined){
		alert('请先选择科目');
		return false;
	}else{
		var objPos = mousePosition(ev);
		var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkAddCommentType()\"><p>所属科目："+sname+"</p><br><p>维度名称：<input type=text id=\"title\" name=\"title\" value=\"\"><label id=\"title_msg\"></label></p><br><p><input type=hidden id=\"sid\" name=\"sid\" value=\""+sid+"\"><input type=submit value=\"添加\" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#title').val('');\" class=\"btn\"></p></form>";
		messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\">"+content+"</div>";
		showMessageBox('添加维度',messContent,objPos,400,0);
	}
}


function checkAddCommentType(){
	if($('#title').val() == ''){
		$('#title_msg').html('<font color=red>请填写维度名称</font>');
		return false;
	}
	if($('#sid').val() == ''){
		$('#title_msg').html('<font color=red>非法操作</font>');
		return false;
	}
	
	if($('#title').val().length>15){
		$('#title_msg').html('<font color=red>维度名称不能超过15个字符</font>');
		return false;
	}

	
}





function testMessageBox_addLevel(ev,requestUrl){
	var objPos = mousePosition(ev);
	var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkAddLevel()\"><p>级别名称：<input type=text id=\"title\" name=\"title\" value=\"\"><label id=\"title_msg\"></label></p><br><p>等级：<input type=text id=\"rank\" name=\"rank\" value=\"\"><label id=\"rank_msg\"></label><span style=\"color:#f16e2b\">（等级越高，评价越高）</span></p><br><p><input type=submit value=\"添加\" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#title').val('');\" class=\"btn\"></p></form>";
	messContent="<div class=\"mesWindowsBox\" style=\"height:150px;\">"+content+"</div>";
	showMessageBox('添加级别',messContent,objPos,500,0);
	
}


function checkAddLevel(){
	if($('#title').val() == ''){
		$('#title_msg').html('<font color=red>请填写级别名称</font>');
		return false;
	}
	if($('#rank').val() == ''){
		$('#rank_msg').html('<font color=red>请填写级别等级</font>');
		return false;
	}
	
	if($('#title').val().length>15){
		$('#title_msg').html('<font color=red>级别名称不能超过15个字符</font>');
		return false;
	}
	
	if(isNaN($('#rank').val())){
		$('#rank_msg').html('<font color=red>等级必须为数字</font>');
		return false;
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



function testMessageBox_deleteCommentType(ev,type,requestUrl){
	var id = '';
	if(type == 'dimension'){
		title = '维度';
		var count = 0;
		$("input[name='dimension_id']:radio").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}else if(type == 'level'){
		title = '级别';
		var count = 0;
		$("input[name='level_id']:radio").each(function(){
			if($(this).attr("checked")){
				count += 1;
				id = $(this).val();
			}
		});
	}
	
	if(confirm("提醒：删除后所有关联数据都将一并删除，确定要删除所选数据吗？")){
		if(id == ''){
			alert('请选择要删除的'+title+'，一次只允许删除一条记录');
			return false;
		}
		if(count > 1){
			alert('一次只能删除一条记录,请去除多余选择');
			return false;
		}else{
			$.ajax({
				type: 'POST',
				url: requestUrl,
				data:{id:id,type:type},
				success:function(data){
					var obj = eval('(' + data + ')');
					if(obj.status==1){
						alert(title+'删除成功');
						location.reload();
					}else{
						alert(title+'删除失败');
					}
				}
			});
		}
	}else{
		return false;
	}

}


function clear_lecture(requestUrl,helu_id){
	if(confirm('清空后所有关联数据都将被删除，确认要清空备课吗?')){
		$.ajax({
				type: 'POST',
				url: requestUrl,
				data:{helu_id:helu_id},
				success:function(data){
					var obj = eval('(' + data + ')');
					if(obj.status==1){
						alert('清空成功');
						location.reload();
					}else{
						alert('清空失败');
					}
				}
			});
	}
}



function testMessageBox_downloadLecture(ev,lecture_id,requestUrl){
	if(lecture_id !=''){
		var objPos = mousePosition(ev);
		var content = '<form method="POST" action="'+requestUrl+'" ><div class="content">';
			content += 	'<div class="down-word-win downWin">';
			content += 	'	<dl>';
			content += 	'		<dt>Word版本</dt>';
			content += 	'		<dd class="clearfix">';
			content += 	'			<span><label><input type="radio" checked="checked" value="docx" name="paper_version"> Word2007/2010，文件扩展名为docx</label></span>';
			content += 	'			<span><label><input type="radio" value="doc" name="paper_version"> Word 2000/2003，文件扩展名为doc</label></span>';
			content += 	'		</dd>';
			content += 	'	</dl>';
			content += 	'	<dl>';
			content += 	'		<dt>纸张大小</dt>';
			content += 	'		<dd>';
			content += 	'			<table cellpadding="0" cellspacing="0">';
			content += 	'				<tbody>';
			content += 	'					<tr class="word-line">';
			content += 	'						<td></td>';
			content += 	'						<td></td>';
			content += 	'						<td></td>';
			content += 	'						<td></td>';
			content += 	'						<td></td>';
			content += 	'						<td></td>';
			content += 	'					</tr>';
			content += 	'					<tr>';
			content += 	'						<td><input type="radio" checked="checked" value="A4" id="g1" name="paper_size"></td>';
			content += 	'						<td><input type="radio" value="A4H" id="g2" name="paper_size"></td>';
			content += 	'						<td><input type="radio" value="A3" id="g3" name="paper_size"></td>';
			content += 	'						<td><input type="radio" value="K16" id="g4" name="paper_size"></td>';
			content += 	'						<td><input type="radio" value="KH8" id="g5" name="paper_size"></td>';
			content += 	'						<td><input type="radio" value="A3H" id="g6" name="paper_size"></td>';
			content += 	'					</tr>';
			content += 	'					<tr>';
			content += 	'						<td><label for="g1">A4</label></td>';
			content += 	'						<td><label for="g2">A4横(双栏)</label></td>';
			content += 	'						<td><label for="g3">A3</label></td>';
			content += 	'						<td><label for="g4">16K</label></td>';
			content += 	'						<td><label for="g5">8K横(双栏)</label></td>';
			content += 	'						<td><label for="g6">A3横(双栏)</label></td>';
			content += 	'					</tr>';
			content += 	'				</tbody>';
			content += 	'			</table>';
			content += 	'		</dd>';
			content += 	'	</dl>';
			content += 	'	<dl>';
			content += 	'		<dt>讲义类型</dt>';
			content += 	'		<dd class="clearfix">';
			content += 	'			<span><label><input type="radio" checked="checked" value="teacher" name="paper_type"> 教师版（有解析答案）</label></span>';
			content += 	'			<span><label><input type="radio" value="student" name="paper_type"> 学生版（无解析答案）</label></span>';
			content += 	'		</dd>';
			content += 	'	</dl>';
			content += 	'	<div class="teacherTips teacherTipsNoBorder cf">温馨提示：下载后的讲义不会带有任何水印，建议您使用微软Word软件打开文档。</div>';
			content += 	'</div>';
			content += 	'</div>';
			content += 	'<div><br><input type="hidden" name="aid" id="aid" value="'+lecture_id+'"><input type=submit value="确定" class="btn"></div></form>';
		messContent="<div class=\"mesWindowsBox\" style=\"height:400px;\">"+content+"</div>";
		showMessageBox('下载讲义',messContent,objPos,770,0);
	}else{
		alert('操作异常');
		return false;
	}
}



function show_report(url){
	window.open(url+"?time="+(new Date()).getTime());
}



function savepic(id,img_url) {
	if (document.all.a1 == null) {
		objIframe = document.createElement("IFRAME");
		document.body.insertBefore(objIframe);
		objIframe.outerHTML = "<iframe name=a"+id+" style='width:400px;hieght:300px' src=" + img_url + "></iframe>";
		re = setTimeout("savepic(id,img_url)", 1)
	}else {
		clearTimeout(re);
		pic = window.open(img_url, "a"+id)
		pic.document.execCommand("SaveAs")
		document.all.a1.removeNode(true)
	}
} 



function testMessageBox_program_useless(ev,requestUrl){
		var objPos = mousePosition(ev);
		messContent="<div class=\"mesWindowsBox\" style=\"height:300px;\"><table style=\"margin:30px 20px;\"><tr height=\"60px;\"><td width=\"70\">无效原因：</td><td><select id=\"reason\" name=\"reason\"><option value=\"\">请选择判定无效的原因</option><option value=\"已结课\">已结课</option><option value=\"已退费\">已退费</option><option value=\"已更换老师\">已更换老师</option><option value=\"其他\">其他</option></select></td></tr><tr><td>无效备注：</td><td><textarea id=\"remark\" name=\"remark\" cols=\"60\" rows=\"3\"></textarea></td></tr><tr height=\"60px;\"><td>&nbsp;</td><td><input type=\"button\" value=\"　确定　\" onclick=\"do_program_useless('"+requestUrl+"')\"></td></tr></table></div>";
		showMessageBox('无效判定',messContent,objPos,550,1);

}


function do_program_useless(requestUrl){
	var reason = $('#reason').val();
	var remark = $('#remark').val();
	if(reason == ''){
		alert('请选择无效原因');
		return false;
	}
	if(reason == '其他' && remark == ''){
		alert('请填写无效备注');
		return false;
	}
	$.ajax({
			type: 'POST',
			url: requestUrl,
			dataType: "json",
			data:{reason:reason,remark:remark},
			success:function(data){
				alert(data.msg);
				if(data.status==1){
					location.reload();
				}
			}
	});
}



function testMessageBox_add_programLesson(ev,requestUrl,saveRatyUrl){
	saveRaty(saveRatyUrl);
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:400px;\"><iframe width=\"600\" height=\"370\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('添加课程',messContent,objPos,650,1);
}


function saveRaty(requestUrl){
	var level_str = '';
	$("input[name='score[]']").each(function(index, element) {
		level_str += $(this).val()+"|"
	});
	$.ajax({
			type: 'POST',
			url: requestUrl,
			data:{level_str:level_str},
			success:function(data){
			}
	});

}

function doAddProgramLesson(new_key,requestUrl){
	var arr = checkProgramLessonForm();
	if(arr['lesson_no'] == ''){
		$('#form_msg').html('<font color=red>请填写课次</font>');
		return false;
	}
	if(isNaN(arr['lesson_no'])){      
		$('#form_msg').html('<font color=red>课次必须为数字</font>');
		return false; 
	}
	if(arr['lesson_difficulty'] == ''){
		$('#form_msg').html('<font color=red>请选择难度</font>');
		return false;
	}
	if(arr['lesson_topic'] == ''){
		$('#form_msg').html('<font color=red>请填写课次主题</font>');
		return false;
	}
	if(arr['lesson_major'] == ''){
		$('#form_msg').html('<font color=red>请填写重难点</font>');
		return false;
	}
	
	$.ajax({
			type: 'POST',
			url: requestUrl,
			dataType: "json",
			data:{lesson_no:arr['lesson_no'],lesson_difficulty:arr['lesson_difficulty'],lesson_topic:arr['lesson_topic'],lesson_major:arr['lesson_major']},
			success:function(data){
				alert(data.msg);
				if(data.status==1){
					//parent.location.reload();
					$("#lesson",window.parent.document).append(data.html);
					window.parent.closeWindow(1);
				}
			}
	});
	

}



function delete_programLesson(key,requestUrl,saveRatyUrl){
	saveRaty(saveRatyUrl);
	if(key == '' || requestUrl == ''){
		alert('非法操作');
		return false;
	}
	$.ajax({
			type: 'POST',
			url: requestUrl,
			dataType: "json",
			data:{key:key},
			success:function(data){
				alert(data.msg);
				//location.reload();
				$('#lesson_'+key).remove();
			}
	});

}


function testMessageBox_edit_programLesson(ev,requestUrl,saveRatyUrl){
	saveRaty(saveRatyUrl);
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:400px;\"><iframe width=\"600\" height=\"370\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('编辑课程',messContent,objPos,650,1);
}



function doEditProgramLesson(key,requestUrl){
	var arr = checkProgramLessonForm();
	if(arr['lesson_no'] == ''){
		$('#form_msg').html('<font color=red>请填写课次</font>');
		return false;
	}
	if(isNaN(arr['lesson_no'])){      
		$('#form_msg').html('<font color=red>课次必须为数字</font>');
		return false; 
	}
	if(arr['lesson_difficulty'] == ''){
		$('#form_msg').html('<font color=red>请选择难度</font>');
		return false;
	}
	if(arr['lesson_topic'] == ''){
		$('#form_msg').html('<font color=red>请填写课次主题</font>');
		return false;
	}
	if(arr['lesson_major'] == ''){
		$('#form_msg').html('<font color=red>请填写重难点</font>');
		return false;
	}
	$.ajax({
		type: 'POST',
		url: requestUrl,
		dataType: "json",
		data:{lesson_no:arr['lesson_no'],lesson_difficulty:arr['lesson_difficulty'],lesson_topic:arr['lesson_topic'],lesson_major:arr['lesson_major']},
		success:function(data){
			alert(data.msg);
			if(data.status==1){
				//parent.location.reload();
				var dificulty_html = '';
				if(data.arr.lesson_difficulty == 1){
					dificulty_html = '★';
				}else if(data.arr.lesson_difficulty == 2){
					dificulty_html = '★★';
				}else{
					dificulty_html = '★★★';
				}
				$("#no_"+key,window.parent.document).html('第'+data.arr.lesson_no+'次课：');
				$("#difficulty_"+key,window.parent.document).html(dificulty_html);
				$("#topic_"+key,window.parent.document).html(data.arr.lesson_topic);
				$("#major_"+key,window.parent.document).html(data.arr.lesson_major);
				window.parent.closeWindow(1);
			}
		}
	});
}


function checkProgramLessonForm(){
	var arr = new Array();
	var reg=new RegExp("\n","g");
	var reg2=new RegExp(" ","g");
	arr['lesson_no'] = $('#lesson_no').val();
	arr['lesson_difficulty'] = $('#lesson_difficulty').val();
	arr['lesson_topic'] = $('#lesson_topic').val();
	arr['lesson_major'] = $("#lesson_major").val().replace(reg,"<br>").replace(reg2,"&nbsp;");
	return arr;
}


function createProgramHtml(requestUrl,saveRatyUrl,returnUrl){
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
	$.ajax({
		type: 'POST',
		url: requestUrl,
		dataType: "json",
		data:{student_code:$('#student_code').val(),student_name:$('#student_name').val(),grade_name:$('#grade_name').val(),teacher_code:$('#teacher_code').val(),teacher_name:$('#teacher_name').val(),classadviser_name:$('#classadviser_name').val(),dept_code:$('#dept_code').val(),dept_name:$('#dept_name').val(),subject_code:$('#subject_code').val(),subject_name:$('#subject_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),start:$('#start').val(),end:$('#end').val(),dimension_id_str:dimension_id_str,dimension_title_str:dimension_title_str,level_str:level_str},
		success:function(data){
			alert(data.msg);
			if(data.status == 1){
				window.location.href = returnUrl;
			}
			
		}
	});
	
}


function testMessageBox_previewImg(ev,imgUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:300px\"><img src=\""+imgUrl+"\" width=\"100%\"></div>";
	showMessageBox('图片预览',messContent,objPos,800,0);
}


/** 保存辅导方案 */

function createProgramHtmlNew(requestUrl,returnUrl){
	var comment=$('#comment').val();
	if(comment == '' || comment == 'undefined')
	{
		alert('请填写老师说内容!');return false;
	}
	var length=$("input[name='lesson_no[]']").length;

	if(length<10)
	{
		alert('至少填写10次课程规划!');return false;
	}

	//课次
	var lesson_no='';
	$("input[name='lesson_no[]']").each(function(index, element) {
		lesson_no += $(this).val()+"|"
	});
	//主题
	var lesson_topic='';
	$("input[name='lesson_topic[]']").each(function(index, element) {
		lesson_topic += $(this).val()+"|"
	});
	//内容
	var lesson_major='';
	$("textarea[name='lesson_major[]']").each(function(index, element) {
		lesson_major += $(this).val()+"|"
	});
	//难度
	var level_str = '';
	$("input[name='score[]']").each(function(index, element) {
		level_str += $(this).val()+"|"
	});
	$.ajax({
		type: 'POST',
		url: requestUrl,
		dataType: "json",
		data:{student_code:$('#student_code').val(),student_name:$('#student_name').val(),grade_name:$('#grade_name').val(),teacher_code:$('#teacher_code').val(),teacher_name:$('#teacher_name').val(),classadviser_name:$('#classadviser_name').val(),dept_code:$('#dept_code').val(),dept_name:$('#dept_name').val(),subject_code:$('#subject_code').val(),subject_name:$('#subject_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),start:$('#start').val(),end:$('#end').val(),lesson_no:lesson_no,level_str:level_str,lesson_topic:lesson_topic,lesson_major:lesson_major,comment:comment},
		success:function(data){
			alert(data.msg);
			if(data.status == 1){
				window.location.href = returnUrl;
			}
			
		}
	});
	
}

	/**
	 * 添加课程规划
	 * @param {[type]} requestUrl [description]
	 * @param {[type]} returnUrl  [description]
	 */
	function addProgram(requestUrl,returnUrl)
	{
		var length=$("input[name='lesson_no[]']").length;
		//课次
		var lesson_no='';
		$("input[name='lesson_no[]']").each(function(index, element) {
			lesson_no += $(this).val()+"|"
		});
		//主题
		var lesson_topic='';
		$("input[name='lesson_topic[]']").each(function(index, element) {
			lesson_topic += $(this).val()+"|"
		});
		//内容
		var lesson_major='';
		$("textarea[name='lesson_major[]']").each(function(index, element) {
			lesson_major += $(this).val()+"|"
		});
	$.ajax({
		type: 'POST',
		url: requestUrl,
		dataType: "json",
		data:{student_code:$('#student_code').val(),student_name:$('#student_name').val(),grade_name:$('#grade_name').val(),teacher_code:$('#teacher_code').val(),teacher_name:$('#teacher_name').val(),classadviser_name:$('#classadviser_name').val(),dept_code:$('#dept_code').val(),dept_name:$('#dept_name').val(),kecheng_code:$('#kecheng_code').val(),kecheng_name:$('#kecheng_name').val(),lesson_no:lesson_no,lesson_topic:lesson_topic,lesson_major:lesson_major},
		success:function(data){
			alert(data.msg);
			if(data.status == 1){
				window.location.href = returnUrl;
			}
			
		}
	});
	}