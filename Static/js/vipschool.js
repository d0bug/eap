
$(function(){
	var i = parseInt($("#num").val());
	var imgwidth = $("#img_width").val();
	var autocut = $("#autocut").val();
	var new_width = $("#new_width").val();
	var new_height = $("#new_height").val();
	for(j=1;j<=i;j++){
		$('#upload_focus_'+j).uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_url").val(),
		'method'   : 'post',
		'formData':{'preview':1,'width':new_width,'height':new_height,type:'file',is_realname:0,autocut:autocut,'num':j},
		'buttonText' : '选择图片',
		'width':'200',
		'multi'    : true,
		'fileTypeDesc' : 'img Files',
		'fileTypeExts' : '*.jpg;*.gif;*.png',
		'fileSizeLimit' : '10240KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#upload_focus_"+obj.num+"_msg").html(obj.status);
			$("#focus_"+obj.num).val(obj.url);
			$("#view_focus_"+obj.num).html("<img src="+obj.show_url+" width=\""+new_width+"\" height=\""+new_height+"\">&nbsp;&nbsp;<a href='#none' onclick=del_file('"+obj.url+"','#view_focus_"+obj.num+"','#focus_"+obj.num+"','#upload_focus_"+obj.num+"_msg','"+$('#delete_url').val()+"')>删除图片</a>");
		}
		});
	}
});


function add_focus(){
	var i = parseInt($("#num").val());
	var imgwidth = $("#img_width").val();
	var autocut = $("#autocut").val();
	var new_width = $("#new_width").val();
	var new_height = $("#new_height").val();
	var js =  "<script>$('#upload_focus_"+(i+1)+"').uploadify({"+
	"'auto'     : true,"+
	"'removeTimeout' : 1,"+
	"'swf'      : '/static/js/uploadify.swf',"+
	"'uploader' : $(\"#upload_url\").val(),"+
	"'method'   : 'post',"+
	"'formData':{'preview':1,'width':new_width,'height':new_height,type:'file','is_realname':0,autocut:autocut,'num':"+(i+1)+"},"+
	"'buttonText' : '选择图片',"+
	"'width':'200',"+
	"'multi'    : false,"+
	"'fileTypeDesc' : 'img Files',"+
	"'fileTypeExts' : '*.jpg;*.gif;*.png',"+
	"'fileSizeLimit' : '10240KB',"+
	"'onUploadSuccess':function(file,data,response){"+
	"	var obj = eval('(' + data + ')');"+
	"	$('#upload_focus_'+obj.num+'_msg').html(obj.status);"+
	"	$('#focus_'+obj.num).val(obj.url);"+
	"	$('#view_focus_'+obj.num).html(\"<img src=\"+obj.show_url+\" width='"+imgwidth+"'>&nbsp;&nbsp;<a href='#none' onclick=del_file('\"+obj.url+\"','#view_focus_\"+obj.num+\"','#focus_\"+obj.num+\"','#upload_focus_\"+obj.num+\"_msg','\"+$('#delete_url').val()+\"')>删除图片</a>\");"+
	"}"+
	"});</script>";

	var html = "<div id=\"divfocus"+(i+1)+"\" class=\"focus\">";
	html+= "焦点图"+(i+1)+"、<br>";
	html+= "<div style=\"padding:10px 30px;\">";
	html+= "<div>";
	html+= "	图片：<div style=\"padding-left:40px\"><span id=\"upload_focus_"+(i+1)+"\" class=\"upload\"></span>";
	html+= "		 <span id=\"view_focus_"+(i+1)+"\" class=\"view_file\"></span>";
	html+= "		 <input type=\"hidden\" id=\"focus_"+(i+1)+"\" name=\"focus[]\" value=\"\">";
	html+= "		 <div id=\"upload_focus_"+(i+1)+"_msg\"></div></div>";
	html+= "</div><br>";
	html+= "<div>";
	html+= "	链接：<input type=\"text\" id=\"link_"+(i+1)+"\" name=\"link[]\" value=\"\" size=\"100\">";
	html+= "</div><br>";
	html+= "<div>";
	html+= "	背景颜色值：<input type=\"text\" id=\"bg_color_"+(i+1)+"\" name=\"bg_color[]\" value=\"\" size=\"30\">";
	html+= "</div>";
	html+= "</div>";
	html+= "<div id=\"focus_msg_"+(i+1)+"\"></div>";
	html+= "<div class=\"delete\"><a href=\"#\" onclick=\"delete_focus("+(i+1)+")\">删除</a></div><input type=\"hidden\" id=\"fid\" name=\"fid[]\" value=\"\"> ";
	html+= js;
	html+= "</div>";

	$("#focus_list").append(html);
	//i = i+1;
	$("#num").val(i+1);
}


function delete_focus(id){
	if(id == ''){
		return false;
	}
	var i = parseInt($("#num").val());
	$("#divfocus"+id).remove();
	$("#num").val(i-1);
}


function check_focus_form(){
	var i = parseInt($("#num").val());
	var j = 0;
	for(x=1;x<=i;x++){
		if($("#focus_"+x).val()== '' && $("#link_"+x).val() != ''){
			$("#focus_msg_"+x).html('<font color=red>请上传焦点图'+x+'的图片</font>');
			return false;
		}
		if( $("#link_"+x).val() == '' && $("#focus_"+x).val()!= '' ){
			$("#focus_msg_"+x).html('<font color=red>请填写焦点图'+x+'的链接</font>');
			return false;
		}
		if( $("#bg_color_"+x).val() == '' ){
			$("#focus_msg_"+x).html('<font color=red>请填写焦点图'+x+'的背景颜色值</font>');
			return false;
		}
		if($("#link_"+x).val() != '' && $("#focus_"+x).val() != ''){
			j = j+1;
		}
	}
	if(j==0){
		alert('请填写焦点图相关信息');
		return false;
	}
}


function del_file(url,divid,inputid,msgid,requestUrl){
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ url:url},
		success:function(data){
			var obj = eval('(' + data + ')');
			if(obj.status==1){
				$(inputid).val('');
				$(divid).html('');
				$(msgid).html('');
			}else{
				alert('删除失败');
			}
		}
	});
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
	if(editorContent == '<br>' || editorContent == ''){
		$('#ncontent_msg').html('请填写内容');
		return false;
	}
}



function testMessageBox_viewTeacher(ev,requestUrl,updateUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;height:550px\"><iframe width=\"760\" height=\"500\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\""+updateUrl+"\" class=\"blue\">修改</a></div>";
	showMessageBox('教师信息',messContent,objPos,800,1);

}


function get_subjects(gid,requestUrl){
	if(gid != ''){
		$.ajax({
			type: 'GET',
			url: requestUrl,
			data:{ gid:gid},
			success:function(data){
				var obj = eval('(' + data + ')');
				$('#subjectHmml').html(obj.html);
				if(obj.status == 0){
					$("#submit").attr('disabled',true);
				}else{
					$("#submit").attr('disabled',false);
				}
			}
		});
	}
}


function recommend_teacher(tid,requestUrl){
	if(tid == ''){
		alert('非法操作');
		return false;
	}
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ tid:tid},
		success:function(data){
			var obj = eval('(' + data + ')');
			alert(obj.msg);
			if(obj.status == 1){
				location.reload();
			}
		}
	});
}


//复制到剪切板js代码
function copyToClipBoard(s) {
	//alert(s);
	if (window.clipboardData) {
		window.clipboardData.setData("Text", s);
		alert("已经复制到剪切板！"+ "\n" + s);
	} else if (navigator.userAgent.indexOf("Opera") != -1) {
		window.location = s;
	} else if (window.netscape) {
		try {
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		} catch (e) {
			alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");
		}
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip)
		return;
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans)
		return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = s;
		str.data = copytext;
		trans.setTransferData("text/unicode", str, copytext.length * 2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip)
		return false;
		clip.setData(trans, null, clipid.kGlobalClipboard);
		alert("已经复制到剪切板！" + "\n" + s)
	}
}


function testMessageBox_sendExpress(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:200px\"><p>快递公司：<select id=\"express_company\" name=\"express_company\"><option value=\"圆通\">圆通</option><option value=\"中通\">中通</option><option value=\"申通\">申通</option><option value=\"顺丰\">顺丰</option><option value=\"韵达\">韵达</option></select></p><br><p>快递单号：<input type=text id=\"express_number\" name=\"express_number\" ></p><br><p><input type=button value=\"确定\" onclick=\"do_sendExpress('"+requestUrl+"')\" class=btn></p></div>";
	showMessageBox('快递发件',messContent,objPos,600,1);
}


function do_sendExpress(requestUrl){
	var express_company = $('#express_company').val();
	var express_number = $('#express_number').val();
	if(express_company == ''){
		alert('请选择快递公司');
		return false;
	}
	if(express_number == ''){
		alert('请填写快递单号');
		return false;
	}
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ express_company:express_company,express_number:express_number},
		success:function(data){
			var obj = eval('(' + data + ')');
			if(obj.status == 1){
				alert('快递发件成功');
				window.location.reload();
			}else{
				alert('快递发件失败');
			}

		}
	});
}


function testMessageBox_viewExpress(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"670\" height=\"500\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('快递信息',messContent,objPos,700,1);
}



function testMessageBox_viewOrder(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"670\" height=\"500\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('订单详情',messContent,objPos,700,1);
}



function testMessageBox_userInfo(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;\"><iframe width=\"670\" height=\"500\" src=\""+requestUrl+"\" style=\"border:0px;\"></iframe></div>";
	showMessageBox('学员信息',messContent,objPos,700,1);
}


function check_add_course_pack(){
	var ptype = $("input[name=ptype]:checked").val();
	if(ptype == 1){
		if($('#course_num').val() == ''){
			$('#course_num_msg').html('<font color=red>请填写课程数量</font>');
			return false;
		}
	}
}


function change_course_list(cid,requestUrl){
	if(cid !== ''){
		$.ajax({
			type: 'GET',
			url: requestUrl,
			data:{ cid:cid},
			success:function(data){
				var obj = eval('(' + data + ')');
				$('#course_id').html(obj.html);
			}
		});
	}
}


function get_last_price(){
	var price = $('#price').val();
	var coupon_type = $("input[name=coupon_type]:checked").val();
	if(price !=''){
		if(coupon_type == 0){//减免
			var coupon_value = $('#coupon_value_'+coupon_type).val();
			if(coupon_value!=''){
				$('#last_price').html('优惠后价格：'+parseInt(price-parseInt(coupon_value))+'元');
			}else{
				alert('请填写优惠幅度');
			}

		}else if(coupon_type == 1){//折扣
			var coupon_value = $('#coupon_value_'+coupon_type).val();
			if(coupon_value!=''){
				$('#last_price').html('优惠后价格：'+parseInt(price*parseFloat(coupon_value)/100)+'元');
			}else{
				alert('请填写优惠幅度');
			}

		}else{
			alert('请选择优惠方式');
		}
	}else{
		alert('请填写原价');
	}

}



function dimission_teacher(tid,requestUrl){
	if(tid == ''){
		alert('非法操作');
		return false;
	}
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data:{ tid:tid},
		success:function(data){
			var obj = eval('(' + data + ')');
			alert(obj.msg);
			if(obj.status == 1){
				location.reload();
			}
		}
	});
}


function testMessageBox_addEmail(ev,requestUrl){
	var objPos = mousePosition(ev);
	messContent="<div class=\"mesWindowsBox\" style=\"height:200px;padding:20px;\"><p>邮件地址：<input type=\"text\" id=\"email\" name=\"email\" value=\"\" size=50><br>(当有新需要赠送教材的订单支付成功时，将会给邮件发送邮件通知)</p><br><p><input type=\"button\" id=\"email\" name=\"email\" value=\"添加邮件\" class=\"btn\" onclick=\"do_addEmail('"+requestUrl+"')\"></p></div>";
	showMessageBox('添加通知邮件',messContent,objPos,500,1);
}


function do_addEmail(requestUrl){
	var email = $('#email').val();
	if(email == ''){
		alert('邮件地址不能为空');
		return false;
	}else{
		var reg = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
		if(reg.test(email)){
			$.ajax({
				type: 'POST',
				url: requestUrl,
				data:{ email:email},
				success:function(data){
					var obj = eval('(' + data + ')');
					alert(obj.msg);
					if(obj.status == 1){
						location.reload();
					}
				}
			});
		}else{
			alert("邮件地址格式错误");
		}

	}
}
