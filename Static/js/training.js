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
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:150px;\"><iframe src=\""+requestUrl+"\" width=\"608\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox(title,messContent,objPos,620,0);
	
}

function testMessageBox_moduleForm(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	var title;
	if(type == 'add'){
		/*if($('#paper_id').val()==''){
			alert('请先选择试卷再添加模块');
			return false;
		}*/
		title = '添加模块';
	}
	if(type == 'edit'){
		title = '编辑模块';
	}
	$.get(requestUrl,
	{paper_id:$('#paper_id').val()},
	function(data){
		messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:150px;\">"+data+"</div>";
		showMessageBox(title,messContent,objPos,550,0);
	});
}



function add_level(divId){
	var num = Number($("#level_num").val());
	var new_num;
	if(num ==0 ) new_num = num+2;
	else
	 new_num = num+1;
	var html = '<span id="span_level_'+new_num+'">';
	html += '考试时间：<input type="text" id="leveltime_'+new_num+'" name="time[]"  value="" >&nbsp;&nbsp;';    
	html += '时长：<input type="text" id="levellong_'+new_num+'" name="long[]" value="" size="2">&nbsp;&nbsp;';
	html += '满分：<input type="text" id="levelscore_'+new_num+'" name="score[]" value="" size="2">&nbsp;&nbsp;';	
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


function del_file_document(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			if(data==1){
				alert('删除成功');
				$(inputid).val('');
				$(divid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
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


	if($('#answer_time').val() == ''){
		alert('答卷时间不能为空');
		return false;
	}

	if(isNaN($('#answer_time').val())){
		alert('答卷时间格式错误，为正整数');
		return false;
	}

	if($('#full_mark').val() == ''){
		alert('请填写满分');
		return false;
	}



	if(isNaN($('#full_mark').val())){
		alert('满分格式错误，为正整数');
		return false;
	}

	if($("#levelname_1").val()==''){

		alert('请填写评级标准');
		return false;
	}
	if($("#levelup_1").val()==''){

		alert('请填写上限');
		return false;
	}

	if($("#levelup_1").val()!= $('#full_mark').val()){

		alert('评级标准上限第一个值必须与满分值相同');
		return false;
	}
	/*$.each($('input[name=up]'), 
    function(n, v) {
    	alert(12);
    	 if($(v).val!='' and n==0){
    	 	alert($(v).val);
    	 	alert(1);
    	 }
    });*/

}


function check_addModule(){
	/*if($('#paper_id').val() == ''){
		alert('请先选择试卷');
		return false;
	}*/
	if($('#name').val() == ''){
		alert('请填写模块名称');
		return false;
	}
	if($('#excellent_strong').val() == ''){
		alert('请填写评优率：较强标准');
		return false;
	}
	if(isNaN($('#key_num').val())){
		alert('关键题格式错误，为整数');
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

function testMessageBox_showAnalysis(ev,analysisUrl,width,height){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:"+height+"px;\"><img src=\""+analysisUrl+"\"></div>";
	showMessageBox('解析原图',messContent,objPos,parseInt(width)+30,0);
}

function btnGetReturnValue_onclick(ev,url){
		var address=window.showModalDialog(url, "", "height=750, width=950, toolbar =no, menubar=no, scrollbars=no, resizable=no, location=no, status=no");
}

function testMessageBox_paper_preview(ev,requestUrl){
	alert(requestUrl);
	var objPos = mousePosition(ev);
	alert(1);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"1000\" height=\"800\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('试卷PDF预览',messContent,objPos,1030,0);
}

function del_img(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			if(data==1){
				alert('删除成功');
				$(inputid).val('');
				$(divid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
}


function testMessageBox_questionInfo(module_id,question_id,requestUrl,is_key){

	$.ajax({
		type: 'POST',
		url: requestUrl,
        data: 'question_id='+question_id+'&key='+is_key+'&module_id='+module_id,
        dataType: "json",
		success:function(data){
			if(data==1){
				alert('操作成功');
				window.location.reload();
			}else{
				alert('操作失败,已超出关键题数量！');
				window.location.reload();
			}
		},
		error:function(){ 
     		alert("error"); 
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

