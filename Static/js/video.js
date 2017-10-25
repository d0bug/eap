
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


function del_url2(url,divid,inputid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			if($.trim(data)==1){
				$(inputid).val('');
				$(divid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
}


function get_attributeTwoList(pid, requestUrl){
	$.ajax({
		type: 'GET',
		url: requestUrl,
		data:{pid:pid,date:new Date().toTimeString()},
		success:function(data){
			$('#attribute_two').html(data)
		}
	});
}


function testMessageBox_reviewVideo(ev,vid,title,attribute_one,attribute_two,duration,introduce,type,user_name,instime,requestUrl){
	var objPos = mousePosition(ev);
	if(vid !=''){
		messContent ="<div class=\"mesWindowsBox\" style=\"height:450px;\"><br>";
		messContent+="  <div class=\"center orange\">"+title+"</div><br>";
		messContent+="  <div style=\"padding-left:100px\">";
		messContent+="  <div class=\"left\">视频属性："+attribute_one+"</div>";
		messContent+="  <div class=\"left\">视频类别："+attribute_two+"</div>";
		messContent+="  <div class=\"left\">视频介绍："+introduce+"</div>";
		messContent+="  <div class=\"left\">视频时长："+duration+"</div>";
		messContent+="  <div class=\"left\">视频格式："+type+"</div>";
		messContent+="  <div class=\"left\">上&nbsp;&nbsp;传&nbsp;&nbsp;人："+user_name+"</div>";
		messContent+="  <div class=\"left\">上传时间："+instime+"</div>";
		messContent+="  </div><br>";
		messContent+="  <div class=\"center\">请选择该讲义是否通过审核？</div><br>";
		messContent+="	<div class=\"center\"><input type=button id=status name=status value=\"通过\" onclick=\"reviewVideo('"+vid+"','1','"+requestUrl+"')\" class=btn>　　　　　<input type=button id=status name=status value=\"打回\" onclick=\"reviewVideo('"+vid+"','2','"+requestUrl+"')\" class=btn></div><br>";
		messContent+="	<div class=\"center\"></div>";
		messContent+="</div>";
		showMessageBox('视频审核',messContent,objPos,570,0);
	}else{
		alert('非法操作');
	}
}


function reviewVideo(vid,status,requestUrl){
	if(vid !=''){
		$.get(requestUrl,
		{vid:vid,status:status,date:new Date().toTimeString()},
		function(data){
			if(data=='0'){
				alert('视频审核失败');
				window.location.reload();
			}else{
				alert('视频审核成功');
				window.location.reload();
			}
		}
		);
	}else{
		alert('非法操作');
	}
}

function testMessageBox_viewVideo(ev,vid,title,attribute_one,attribute_two,duration,introduce,type,user_name,instime){
	var objPos = mousePosition(ev);
	if(vid !=''){
		messContent ="<div class=\"mesWindowsBox\" style=\"height:300px;\"><br>";
		messContent+="  <div class=\"center orange\">"+title+"</div><br>";
		messContent+="  <div style=\"padding-left:100px\">";
		messContent+="  <div class=\"left\">视频属性："+attribute_one+"</div>";
		messContent+="  <div class=\"left\">视频类别："+attribute_two+"</div>";
		messContent+="  <div class=\"left\">视频介绍："+introduce+"</div>";
		messContent+="  <div class=\"left\">视频时长："+duration+"</div>";
		messContent+="  <div class=\"left\">视频格式："+type+"</div>";
		messContent+="  <div class=\"left\">上&nbsp;&nbsp;传&nbsp;&nbsp;人："+user_name+"</div>";
		messContent+="  <div class=\"left\">上传时间："+instime+"</div>";
		messContent+="  </div>";
		messContent+="</div>";
		showMessageBox(title,messContent,objPos,570,0);
	}else{
		alert('非法操作');
	}
}



function select_attributeTwo(attribute_one,attribute_two_divid,requestUrl){
	if(attribute_one != ''){
		$.get(requestUrl,
		{attribute_one:attribute_one, _tm:(new Date()).getTime()},
		function(data){
			$(attribute_two_divid).html(data);
		}
		);
	}
}



function testMessageBox_playVideo(ev,title,requestUrl){
	var objPos = mousePosition(ev);
	messContent ="<div class=\"mesWindowsBox\" style=\"height:600px;\"><br>";
	messContent+="  <div class=\"left orange\">视频名称："+title+"</div>";
	messContent+="  <iframe width=\"760\" height=\"550\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe>";
	messContent+="</div>";
	showMessageBox("视频播放窗口",messContent,objPos,780,0);

}


function testMessageBox_addAttribute(ev,type,requestUrl){
	if(type != ''){
		var title;
		if(type == 'attribute_one'){
			var dataStr = {type:type};
			title = '添加视频属性';
		}
		if(type == 'attribute_two'){
			var attribute_one = '';
			if($("input[name='attribute_one']:checked").val()!=undefined){
				attribute_one = $("input[name=attribute_one]:checked").val();
			}
			var dataStr = {type:type,attribute_one:attribute_one};
			title = '添加视频类别';
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
	if(type == 'attribute_two'){
		title = '类别';
	}
	if($('#name').val()==''){
		$('#name_msg').html('视频'+title+'名称不能为空');
		return false;
	}else{
		$('#name_msg').html('');
	}
}


function deleteAttribute(type,requestUrl){
	var confirmMsg;
	var attributeName;
	if(type=='attribute_one'){
		var attribute_one_id = '';
		if($("input[name='attribute_one']:checked").val()!=undefined){
			attribute_one_id = $("input[name=attribute_one]:checked").val();
		}
		if(attribute_one_id == ''){
			alert('请选择要删除的视频属性');
			return false;
		}
		attributeName = $("input[name='attribute_one']:checked").attr('title');
		confirmMsg = '删除该视频属性后，目录下相关联的视频类别同时也将自动删除！\n确认删除视频属性下的“'+attributeName+'”吗？';
		var dataStr = {type:type,attribute_one:attribute_one_id}
	}else if(type=='attribute_two'){
		var attribute_two_id = '';
		if($("input[name='attribute_two']:checked").val()!=undefined){
			attribute_two_id = $("input[name=attribute_two]:checked").val();
		}
		if(attribute_two_id == ''){
			alert('请选择要删除的视频类别');
			return false;
		}
		attributeName = $("input[name='attribute_two']:checked").attr('title');
		confirmMsg = '确认删除视频类别下的“'+attributeName+'”吗？';
		var dataStr = {type:type,attribute_two:attribute_two_id}
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
		);
	}
}


function testMessageBox_editAttribute(ev,type,requestUrl){
	var id = '';
	if(type == 'attribute_one'){
		title = '视频属性';
		id = $("input[name=attribute_one]:checked").val();
	}else if(type == 'attribute_two'){
		title = '视频类别';
		id = $("input[name=attribute_two]:checked").val();
	}
	if(id == '' || id == undefined){
		alert('请选择要编辑'+title+'，一次只允许编辑一条记录');
		return false;
	}

	var name = $("#"+type+id).attr('title');
	var objPos = mousePosition(ev);
	var content = "<form method=\"POST\" action=\""+requestUrl+"\" onsubmit=\"return checkEditAttributeName('"+title+"')\" style=\"padding: 30px 40px;\"><p><input type=text id=\"name\" name=\"name\" value=\""+name+"\" size=\"30\"><label id=\"name_msg\"></label></p><br><p><input type=hidden name=\"type\" value=\""+type+"\"><input type=hidden name=\"aid\" value=\""+id+"\"><input type=submit value=\"修改\" class=\"btn\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"取消\" onclick=\"javascript:$('#name').val('');\" class=\"btn\"></p></form>";
	messContent="<div class=\"mesWindowsBox\" style=\"height:250px;\">"+content+"</div>";
	showMessageBox('编辑'+title+'名称',messContent,objPos,450,0);
}


function checkEditAttributeName(title){
	if($("#name").val()==''){
		$("#name_msg").html('<font color=red>'+title+'名称不能为空</font>');
		return false;
	}else{
		return true;
	}
}

