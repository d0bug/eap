function get_moduleList(paper_id,requestUrl){
	$.get(requestUrl,
	{paper_id:paper_id},
	function(data){
		var obj = eval('(' + data + ')');
		$('#paper_edit').html(obj.operate);
		$('#module_id').html(obj.moduleHtml);
		$('#module_edit').html('');
	}
	);
}


function get_module_edit(module_id,requestUrl){
	$('#module_edit').html('<a href="#" onclick="testMessageBox_moduleForm(event,\'edit\',\''+requestUrl+'/module_id/'+module_id+'\');" class="blue">编辑模块</a>');
}

function testMessageBox_paperForm(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	var title;
	if(type == 'add'){
		title = '添加试卷';
	}
	if(type == 'edit'){
		title = '编辑试卷';
	}
	$.get(requestUrl,
	function(data){
		messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:300px;\">"+data+"</div>";
		showMessageBox(title,messContent,objPos,550,0);
	});
}


function testMessageBox_moduleForm(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	var title;
	if(type == 'add'){
		if($('#paper_id').val()==''){
			alert('请先选择试卷再添加模块');
			return false;
		}
		title = '添加模块';
	}
	if(type == 'edit'){
		title = '编辑模块';
	}
	$.get(requestUrl,
	{paper_id:$('#paper_id').val()},
	function(data){
		messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:300px;\">"+data+"</div>";
		showMessageBox(title,messContent,objPos,550,0);
	});
}



function add_level(divId){
	var num = Number($("#level_num").val());
	var new_num = num+1;
	var html = '<span id="span_level_'+new_num+'">';
	html += '名称：<input type="text" id="level_'+new_num+'" name="name[]" value="" size="10">&nbsp;&nbsp;';
	html += '下限：<input type="text" id="level_'+new_num+'" name="low[]" value="" size="5">&nbsp;&nbsp;';
	html += '说明：<input type="text" id="level_'+new_num+'" name="desc[]" value="" size="20">&nbsp;&nbsp;';
	html += '<a href="#" onclick="del_level(\'#span_level_'+new_num+'\')"><img src="/static/images/delete.png"></a><br></span>';
	$("#levelSpan").append(html);
	$("#level_num").val(new_num);
}


function del_level(spanId){
	var num = Number($("#level_num").val());
	var new_num = num-1;
	$(spanId).remove();
	$("#level_num").val(new_num);
}


function check_addPaper(){
	if($('#title').val() == ''){
		alert('请填写试卷名称');
		return false;
	}
	if($('#question_num').val() == ''){
		alert('请填写试题数量');
		return false;
	}
	if(isNaN($('#question_num').val())){
		alert('试题数量格式错误');
		return false;
	}
	if(Number($('#level_num').val())==0){
		alert('评级标准不能为空');
		return false;
	}
	if($('input:text[name="name[]"]').val() == ''){
		alert('评级标准名称不能为空');
		return false;
	}
	if($('input:text[name="low[]"]').val() == ''){
		alert('评级标准下限不能为空');
		return false;
	}
	if($("input[name='is_accuracy']:checked").val()==undefined){
		alert('请选择采用正确率');
		return false;
	}
}


function check_addModule(){
	if($('#paper_id').val() == ''){
		alert('请先选择试卷');
		return false;
	}
	if($('#name').val() == ''){
		alert('请填写模块名称');
		return false;
	}
	if($('#excellent_strong').val() == ''){
		alert('请填写评优率：较强标准');
		return false;
	}
	if(isNaN($('#excellent_strong').val())){
		alert('评优率：较强标准格式错误');
		return false;
	}
	if($('#excellent_weak').val() == ''){
		alert('请填写评优率：较弱标准');
		return false;
	}
	if(isNaN($('#excellent_weak').val())){
		alert('评优率：较弱标准格式错误');
		return false;
	}
	if($('#accuracy').val() == ''){
		alert('请填写虚拟评优率');
		return false;
	}
	if(isNaN($('#accuracy').val())){
		alert('虚拟评优率格式错误');
		return false;
	}
	
}


function testMessageBox_questionForm(ev,type,requestUrl){
	var paper_id = $('#paper_id').val();
	var module_id = $('#module_id').val();
	if(type == 'add'){
		if(paper_id == ''){
			alert('请先选择试卷');
			return false;
		}
		if(module_id == ''){
			alert('请先选择模块');
			return false;
		}
		title = '添加试题';
	}else{
		paper_id = 0;
		module_id = 0;
		title = '编辑试题';
	}
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:300px;\"><iframe src=\""+requestUrl+"/paper_id/"+paper_id+"/module_id/"+module_id+"\" width=\"600\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox(title,messContent,objPos,630,1);
}


function testMessageBox_statisticInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:350px;\"><iframe src=\""+requestUrl+"\" width=\"600\" height=\"350\" style=\"border:0px\"></iframe></div>";
	showMessageBox('题目统计信息',messContent,objPos,630,0);
}


function testMessageBox_showImg(ev,imgUrl,width,height){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:"+height+"px;\"><img src=\""+imgUrl+"\"></div>";
	showMessageBox('题干原图',messContent,objPos,parseInt(width)+30,0);
}


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


function get_answerArr(optionNum,requestUrl){
	$.get(requestUrl,
	{option_num:optionNum},
	function(data){
		$('#answer').html(data);
	}
	);
}