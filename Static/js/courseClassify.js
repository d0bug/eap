//视频缩略图
function del_object(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			var obj = eval('(' + data + ')');
			if(obj.status==1){
				$(inputid).val('');
				$(divid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
}
function del_file(url,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			var obj = eval('(' + data + ')');
			if(obj.status==1){
				$("#handout_url").val('');
				$("#upload_file_msg").html('');
				$("#upload_file_url").html('');
				$("#del_file_url").html('');
			}else{
				alert('删除失败');
			}
		}
	});
}
function testMessageBox_viewCourse(ev,requestUrl,title){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"760\" height=\"500\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox(title,messContent,objPos,800,1);

}

function select_isFree_radio(){
	$("input[name='is_free']").eq(1).attr('checked',true);
}
function price_input(is_free){
	if(is_free == 1){
		$("input[name='price']").attr('disabled',false);
		$("input[name='price']").focus();
	}else{
		$("input[name='price']").val('');
	}
}
function select_teacher(requestId,requestUrl){
	var gid = '';
	var sid = '';
	var dataStr= '';
	gid = $("input[name='grade']:checked").val();
	sid = $("input[name='subject']:checked").val();

	if(gid != undefined && sid != undefined){
		dataStr = {grade:gid,subject:sid};
		$.post(requestUrl,
		dataStr,
		function(data){
			$(requestId).show();
			$(requestId).html(data);
		}
		)
	}else{
		alert("请选择相应的学科和学部");
	}
}
function course_info_change(type,selfId,replaceId,requestUrl){

	if(type == 'subject'){
		dataStr = {type:type,grade:$("#"+selfId+" option:selected").val()};
	}else if(type == 'classify'){
		dataStr = {type:type,subject:$("#"+selfId+" option:selected").val()};
	}else if(type == 'twoClassify'){
		dataStr = {type:type,classify:$("#"+selfId+" option:selected").val()};
	}else if(type == 'threeClassify'){
		dataStr = {type:type,classify:$("#"+selfId+" option:selected").val()};
	}
	$.post(requestUrl,
	dataStr,
	function(data){
		$("#"+replaceId).html(data)
	}
	)

}
function delete_course_info(type,requestUrl){
	var confirmMsg;
	var attributeName;
	if(type=='grade'){
		var gid = '';
		if($("input[name='grade']:checked").val()!=undefined){
			gid = $("input[name=grade]:checked").val();
		}
		if(gid == ''){
			alert('请选择要删除的学部');
			return false;
		}
		gradeName = $("input[name='grade']:checked").attr('title');
		confirmMsg = '删除该学部后，目录下相关联的学部和类别同时也将自动删除！\n确认删除学部下的“'+gradeName+'”吗？';
		var dataStr = {type:type,grade:gid}
	}else if(type=='subject'){
		var sid = '';
		if($("input[name='subject']:checked").val()!=undefined){
			sid = $("input[name=subject]:checked").val();
		}
		if(sid == ''){
			alert('请选择要删除的学科');
			return false;
		}
		subjectName = $("input[name='subject']:checked").attr('title');
		confirmMsg = '删除该学科后，目录下相关联的类别同时也将自动删除！\n确认删除学科下的“'+subjectName+'”吗？';
		var dataStr = {type:type,subject:sid}
	}else if(type=='classify'){
		var cid = '';
		if($("input[name='classify']:checked").val()!=undefined){
			cid = $("input[name=classify]:checked").val();
		}
		if(cid == ''){
			alert('请选择要删除的分类');
			return false;
		}

		classifyName = $("input[name='classify']:checked").attr('title');
		confirmMsg = '删除该分类后，目录下相关联的二级分类同时也将自动删除！\n确认删除学科下的“'+classifyName+'”吗？';
		var dataStr = {type:type,classify:cid};
	}else if(type=='twoClassify'){
		var tcid = '';
		if($("input[name='twoClassify']:checked").val()!=undefined){
			tcid = $("input[name=twoClassify]:checked").val();
		}
		if(tcid == ''){
			alert('请选择要删除的二级分类');
			return false;
		}

		classifyName = $("input[name='twoClassify']:checked").attr('title');
		confirmMsg = '确认删除二级分类的“'+classifyName+'”吗？';
		var dataStr = {type:type,twoClassify:tcid};
	}else if(type=='threeClassify'){
		var tcid = '';
		if($("input[name='threeClassify']:checked").val()!=undefined){
			tcid = $("input[name=threeClassify]:checked").val();
		}
		if(tcid == ''){
			alert('请选择要删除的三级分类');
			return false;
		}

		classifyName = $("input[name='threeClassify']:checked").attr('title');
		confirmMsg = '确认删除三级分类的“'+classifyName+'”吗？';
		var dataStr = {type:type,threeClassify:tcid};
	}else if(type=='fourClassify'){
		var tcid = '';
		if($("input[name='fourClassify']:checked").val()!=undefined){
			tcid = $("input[name=fourClassify]:checked").val();
		}
		if(tcid == ''){
			alert('请选择要删除的四级分类');
			return false;
		}

		classifyName = $("input[name='fourClassify']:checked").attr('title');
		confirmMsg = '确认删除四级分类的“'+classifyName+'”吗？';
		var dataStr = {type:type,fourClassify:tcid};
	}

	if(!confirm(confirmMsg)){
		return false;
	}else{
		$.post(requestUrl,
		dataStr,
		function(data){
			var obj = eval('(' + data + ')');
			alert(obj.msg);
			if(obj.status == 1){
				window.location.reload();
			}
		}
		)
	}
}

function testMessageBox_addClassify(ev,type,requestUrl){

	if(type != ''){
		var title;
		if(type == 'grade'){
			var dataStr = {type:type};
			title = '添加学部';
		}
		if(type == 'subject'){
			var grade = '';
			if($("input[name='grade']:checked").val()!=undefined){
				grade = $("input[name=grade]:checked").val();
			}
			var dataStr = {type:type,grade:grade};
			title = '添加学科';
		}
		if(type == 'classify'){
			var grade = '';
			var subject = '';
			if($("input[name='grade']:checked").val()!=undefined && $("input[name='subject']:checked").val()!=undefined){
				grade = $("input[name=grade]:checked").val();
				subject = $("input[name=subject]:checked").val();
			}

			var dataStr = {type:type,grade:grade,subject:subject};
			title = '添加分类';
		}
		if(type == 'twoClassify'){
			var grade = '';
			var subject = '';
			var classify =''
			if($("input[name='grade']:checked").val()!=undefined && $("input[name='subject']:checked").val()!=undefined && $("input[name='classify']:checked").val()!=undefined){
				grade = $("input[name=grade]:checked").val();
				subject = $("input[name=subject]:checked").val();
				classify = $("input[name=classify]:checked").val();
			}

			var dataStr = {type:type,grade:grade,subject:subject,classify:classify};
			title = '添加二级分类';
		}
		if(type == 'threeClassify'){
			var grade = '';
			var subject = '';
			var classify ='';
			var twoClassify =''
			if($("input[name='grade']:checked").val()!=undefined && $("input[name='subject']:checked").val()!=undefined && $("input[name='classify']:checked").val()!=undefined && $("input[name='twoClassify']:checked").val()!=undefined){
				grade = $("input[name=grade]:checked").val();
				subject = $("input[name=subject]:checked").val();
				classify = $("input[name=classify]:checked").val();
				twoClassify = $("input[name=twoClassify]:checked").val();
			}

			var dataStr = {type:type,grade:grade,subject:subject,classify:classify,twoClassify:twoClassify};
			title = '添加三级分类';
		}
		if(type == 'fourClassify'){
			var grade = '';
			var subject = '';
			var classify ='';
			var twoClassify =''
			var threeClassify =''
			if($("input[name='grade']:checked").val()!=undefined && $("input[name='subject']:checked").val()!=undefined && $("input[name='classify']:checked").val()!=undefined && $("input[name='twoClassify']:checked").val()!=undefined && $("input[name='threeClassify']:checked").val()!=undefined){
				grade = $("input[name=grade]:checked").val();
				subject = $("input[name=subject]:checked").val();
				classify = $("input[name=classify]:checked").val();
				twoClassify = $("input[name=twoClassify]:checked").val();
				threeClassify = $("input[name=threeClassify]:checked").val();
			}

			var dataStr = {type:type,grade:grade,subject:subject,classify:classify,twoClassify:twoClassify,threeClassify:threeClassify};
			title = '添加四级分类';
		}
		$.get(requestUrl,
		dataStr,
		function(data){
			var objPos = mousePosition(ev);
			messContent = "<div class=\"mesWindowsBox\" style=\"height:auto;min-height:250px\">"+data+"</div>";
			showMessageBox(title,messContent,objPos,550,0);
		}
		);

	}
}

function check_add_item(type){
	if(type == 'attribute_two' ){
		if( $('#parent_id').val()==''){
			$('#attribute_one_msg').html('所属视频属性不能为空');
			return false;
		}else{
			$('#attribute_one_msg').html('');
		}

	}
	var title = '属性';
	if(type == 'grade'){
		title = '学部';
	}else if(type == 'subject'){
		title = '学科';
		if($('#grade').val() == ''){
			$('#attribute_one_msg').html('学部不能为空');
			return false;
		}
		if($('#alias2').val() == ''){
			$('#alias2_msg').html('学科别名不能为空');
			return false;
		}
	}else if(type == 'grade'){
		title = '学部';
	}else if(type == 'classify'){
		title = '分类';
		if($('#gradeSelect').val() == ''){
			$('#attribute_one_msg').html('学部不能为空');
			return false;
		}
		if($('#subjectSelect').val() == ''){
			$('#attribute_two_msg').html('学科不能为空');
			return false;
		}
	}else if(type == 'twoClassify'){
		title = '分类';
		if($('#gradeSelect').val() == ''){
			$('#attribute_one_msg').html('学部不能为空');
			return false;
		}
		if($('#subjectSelect').val() == ''){
			$('#attribute_two_msg').html('学科不能为空');
			return false;
		}
		if($('#classifySelect').val() == ''){
			$('#classifySelect_msg').html('一级分类不能为空');
			return false;
		}
	}else if(type == 'threeClassify'){
		title = '分类';
		if($('#gradeSelect').val() == ''){
			$('#attribute_one_msg').html('学部不能为空');
			return false;
		}
		if($('#subjectSelect').val() == ''){
			$('#attribute_two_msg').html('学科不能为空');
			return false;
		}
		if($('#classifySelect').val() == ''){
			$('#classifySelect_msg').html('一级分类不能为空');
			return false;
		}
		if($('#twoClassifySelect').val() == ''){
			$('#twoClassifySelect_msg').html('二级分类不能为空');
			return false;
		}
	}

	if($('#name').val()==''){
		$('#name_msg').html(title+'名称不能为空');
		return false;
	}else if($('#alias').val()==''){
		if(type == 'classify'){
			$('#alias_msg').html('关键字不能为空');
		}else{
			$('#alias_msg').html('英文全拼不能为空');
		}
		return false;
	}else{
		$('#name_msg').html('');
	}
}

function select_course_info(type,gid,sid,cid,request_div_id,requestUrl){

	if(type == 'subject'){
		var sendData = {type:2,grade:gid, _tm:(new Date()).getTime()};
	}else if(type == 'classify'){
		var sendData = {type:3,grade:gid,subject:sid, _tm:(new Date()).getTime()};
	}else if(type == 'twoClassify'){
		var sendData = {type:4,grade:gid,subject:sid,classify:cid, _tm:(new Date()).getTime()};
	}else if(type == 'threeClassify'){
		var sendData = {type:5,grade:gid,subject:sid,twoClassify:cid, _tm:(new Date()).getTime()};
	}else if(type == 'fourClassify'){
		var sendData = {type:6,grade:gid,subject:sid,threeClassify:cid, _tm:(new Date()).getTime()};
	}else{
		return false;
	}
	$.get(requestUrl,sendData,
	function(data){
		$(request_div_id).html(data);
		switch(type){
			case 'subject':
			$('#classify_div').html('请先选择学科！');
			$('#twoClassify_div').html('请先选择一级分类！');
			$('#threeClassify_div').html('请先选择二级分类！');
			break;
			case 'classify':
			$('#twoClassify_div').html('请先选择一级分类！');
			$('#threeClassify_div').html('请先选择二级分类！');
			break;
			case 'twoClassify':
			$('#threeClassify_div').html('请先选择二级分类！');
			break;
			case 'threeClassify':
			$('#fourClassify_div').html('请先选择三级分类！');
			break;
		}
	}
	);

}


function testMessageBox_edit_classify(ev,type,requestUrl){
	var id = '';
	if(type == 'grade'){
		title = '学部';
		id = $("input[name=grade]:checked").val();
	}else if(type == 'subject'){
		title = '学科';
		id = $("input[name=subject]:checked").val();
	}else if(type == 'classify'){
		title = '分类';
		id = $("input[name=classify]:checked").val();
	}else if(type == 'twoClassify'){
		title = '二级分类';
		id = $("input[name=twoClassify]:checked").val();
	}else if(type == 'threeClassify'){
		title = '三级分类';
		id = $("input[name=threeClassify]:checked").val();
	}else if(type == 'fourClassify'){
		title = '四级分类';
		id = $("input[name=fourClassify]:checked").val();
	}
	if(id == '' || id == undefined){
		alert('请选择要编辑'+title+'，一次只允许编辑一条记录');
		return false;
	}

	var name = $("#"+type+id).attr('title');
	var objPos = mousePosition(ev);
	var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkEditClassifyName('"+title+"')\" style=\"padding: 30px 40px;\"><p><input type=text id=\"name\" name=\"name\" value=\""+name+"\" size=\"30\"><label id=\"name_msg\"></label></p><br><p><input type=hidden name=\"type\" value=\""+type+"\"><input type=hidden name=\"typeId\" value=\""+id+"\"><input type=submit value=\"修改\" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#name').val('');\" class=\"btn\"></p></form>";
	messContent="<div class=\"mesWindowsBox\" style=\"height:250px;\">"+content+"</div>";
	showMessageBox('编辑'+title+'名称',messContent,objPos,450,0);
}

function checkEditClassifyName(title){
	if($("#name").val()==''){
		$("#name_msg").html('<font color=red>'+title+'名称不能为空</font>');
		return false;
	}else{
		return true;
	}
}


function check_course_form(){//alert($('#tid').val());
	if($('#tid').val() ==  ''){
		//$('#tid_msg').html('主讲教师不能为空');
		//return false;
	}
}


function change_teacher(sid,requestUrl){
	var gid = $("input[name=grade]:checked").val();
	if(gid !== '' && sid != ''){
		$.ajax({
			type: 'POST',
			url: requestUrl,
			data:{ subject:sid,grade:gid},
			success:function(data){
				$('#tid').html(data);
			}
		});
	}else{
		alert('操作异常');
	}

}



