
$(function () {
	$("#checkAll_grade").click(function () { 
		if(this.checked){
			$("#grade :checkbox").attr("checked", true); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}else{
			$("#grade :checkbox").attr("checked", false); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}
	});
	$("#checkAll_subject").click(function () { 
		if(this.checked){
			$("#dept :checkbox").attr("checked", true); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}else{
			$("#dept :checkbox").attr("checked", false); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}
	});
	$("#checkAll_data").click(function () { 
		if(this.checked){
			$("#apply_table :checkbox").attr("checked", true); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}else{
			$("#apply_table :checkbox").attr("checked", false); //"#div1 :checked"之间必须有空格checked是设置选中状态。如果为true则是选中fo否则false为不选中
		}
	});

});


function hide(divid){
	$(divid).hide();
}

function show(divid){
	$(divid).show();
}

function toggle(divid,btnid){
	$(divid).toggle();
	if($(divid).css('display')=='none'){
		$(btnid).val('展开');
	}else{
		$(btnid).val('收起');
	}
}

function change_hidden_type(val,hiddenId){
	$(hiddenId).val(val);
}

function required_to_display(id,num){
	if(!!$("#"+id).attr("checked")){
		$("#display"+num).attr("checked",true);
	}
}

function  display_to_require(id,num){
	if(!$("#"+id).attr("checked")){
		$("#required"+num).attr("checked",false);
		if(num == 3){
			$("#grade :checkbox").attr("checked", false);
		}
		if(num == 4){
			$("#dept :checkbox").attr("checked", false);
		}
	}

}

function copy_clip(divid) {
	var txt = $(divid).text();
	if (window.clipboardData) {
		window.clipboardData.clearData();
		window.clipboardData.setData("Text", txt);
	} else if (navigator.userAgent.indexOf("Opera") != -1) {
		window.location = txt;
	} else if (window.netscape) {
		try {
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		} catch (e) {
			alert("您的firefox安全限制限制您进行剪贴板操作，请在新窗口的地址栏里输入'about:config'然后找到'signed.applets.codebase_principal_support'设置为true'");
			return false;
		}
		var clip = Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard);
		if (!clip)
		return;
		var trans = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);
		if (!trans)
		return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = txt;
		str.data = copytext;
		trans.setTransferData("text/unicode", str, copytext.length * 2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip)
		return false;
		clip.setData(trans, null, clipid.kGlobalClipboard);
	}
}


function checkUploadTeacher(){
	var teacherList = $("#teacherlist").val();
	if(teacherList == ''){
		$("#file_msg").html('请选择要上传的教师资料');
		return false;
	}
	return true;
}

