<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/ueditor1_4_3/themes/default/_css/ueditor.css" type="text/css" rel="stylesheet">
<style>
<!--
.success {
	background-color: #DFF0D8;	
} 
-->
</style>
</head>
<body style="padding: 5px;">
	<div class="easyui-layout" data-options="fit: true">
		<div region="center" data-options="collapsible: false, border: false">
			<table cellpadding="0" cellspacing="0" border="0" width="100%"
				class="tableInfo">
				<tr>
					<td class="wd_120 alt right">年部：</td>
					<td>
						<ul id="question-add-grade" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">学科：</td>
					<td>
						<ul id="question-add-subject" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">教材版本：</td>
					<td>
						<ul id="question-add-knowledge-type" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">课程类型：</td>
					<td>
						<ul id="question-add-course-type" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">题型：</td>
					<td>
						<ul id="question-add-question-type" class="fliter_box_select"></ul>
					</td>
				</tr>
			</table>
			<form id="question-add-form" method="post" novalidate>
				<input type="hidden" id="subject_id" name="subject_id" value="" />
				<input type="hidden" id="course_type_id" name="course_type_id" value="" />
				<input type="hidden" id="question_type_id" name="question_type_id" value="" />
				<input type="hidden" id="question_type_code" name="question_type_code" value="" />
				<input type="hidden" id="uid" name="uid" value="" />
				<input type="hidden" id="sdate" name="sdate" value="" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr>
						<td class="wd_120 alt right"><span class="red">*</span> 主知识点：</td>
						<td class="wd_300"><input id="question-add-knowledge" name="knowledge_id" class="easyui-combotree" style="width: 250px;" data-options="required: true, method: 'get', lines: true, valueField: 'id', textField: 'title'" />
						</td>
						<td class="wd_120 alt right"><span class="red">*</span> 难度：</td>
						<td>
							<div id="question-add-difficulty"></div>
						</td>
					</tr>
					<tr>
						<td class="alt right">副知识点：</td>
						<td><input id="question-add-subknowledge"
							name="sub_knowledge_id[]" class="easyui-combotree"
							style="width: 250px;"
							data-options="cascadeCheck: false, method: 'get', lines: true"
							multiple /></td>
						<td class="alt right"><span class="red">*</span> 适用年级：</td>
						<td><input id="question-add-grades" name="grades[]"
							class="easyui-combobox" style="width: 250px;"
							data-options="required: true, url: '/Question/Basic/getGradesByGroup', editable: false, multiple: true, valueField: 'id', textField: 'title'" />
						</td>
					</tr>
					<tbody id="html"></tbody>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;"
			data-options="collapsible: false, split: false, border: false">
			<a href="#" class="easyui-linkbutton"
				data-options="plain:true, iconCls:'icon-save'"
				onclick="check_question();">提交</a>
		</div>
	</div>
	<!--Begin 加子题对话框-->
	<div id="question-add-sub-question-dlg" class="easyui-dialog"
		data-options="title:'子题',iconCls:'icon-add',modal:true,closed:true"
		style="width: 1000px; height: 500px; padding: 5px;">
		<iframe scrolling="auto" id="question-add-sub-question-dlg-iframe"
			frameborder="0" src="" style="width: 100%; height: 100%;"></iframe>
	</div>
	<!--End 添加子题对话框-->
	<!--Begin 试题预览-->
	<div id="question-add-preview-dlg" class="easyui-dialog"
		style="width: 800px; height: 500px; padding: 5px;" title="预览"
		data-options="iconCls:'icon-add',modal:true, closed:true, buttons:'#question-add-preview-dlg-buttons'"></div>
		<div id="question-add-preview-dlg-buttons">
			<a href="#" class="easyui-linkbutton" iconCls="icon-ok"
				onclick="save_question()">提交</a>
		</div>
	<!--End 试题预览-->
	<object id="tiku_plugin" type="application/x-tkbsplugin" width="1"
		height="1" style="width: 0px; height: 0; overflow: hidden;">
		<param name="onload" value="pluginLoaded" />
	</object>
	<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script>
	<script type="text/javascript" src="/static/js/ue.ext.openinword.js"></script>
	<script type="text/javascript" src="/static/js/question.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min.js"></script>
	<script type="text/javascript">
	function preview_question() {
		$('#question-add-preview-dlg').dialog({
			href: '/Question/Knowledge/preview_question'
		}).dialog('open');
	}
	function save_question() {
		$('#question-add-form').form('submit', {
			url: '/Question/Knowledge/save_question',
			onSubmit: function () {
				return $(this).form('validate');
			},
			success: function (result) {
				result = JSON.parse(result);
				if (result.status) {
					get_question_unique();
					$.messager.alert('提示信息', '添加成功!', 'info');
					$('#question-add-preview-dlg').dialog('close');
					$('#question-add-question-type li:first').click();
				}
				else {
					$.messager.alert('错误信息', '操作失败!', 'error');
				}
			}
		});
	}
	var _initGrade = function() {
		$.post('/Question/Basic/getGrades', null, function(data){
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				$('#question-add-grade').append('<li onclick="_initSubject(' + row.id + ');_reRenderStyle(this);$(\'#subject_id, #course_type_id, #question_type_id\').val(\'\');"><a href="#">' + row.title + '</a></li>');
			});
			$('#question-add-grade li:first').click();
		});
	}
	var _initSubject = function(gradeId) {
		$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data){
			$('#question-add-subject, #question-add-course-type, #question-add-knowledge-type, #question-add-question-type').html('');
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				$('#question-add-subject').append('<li onclick="_initKnowledgeType(' + row.id + ');_initQuestionType(' + row.id + ');_reRenderStyle(this);$(\'#course_type_id, #question_type_id\').val(\'\');$(\'#subject_id\').val(\'' + row.id + '\');"><a href="#">' + row.title + '</a></li>');
			});
			if ($('#question-add-subject li').length == 0) {
				_initKnowledge('');
			}
			else {
				$('#question-add-subject li:first').click();
			}
		});
	}
	var _initKnowledgeType = function(subjectId) {
			$.post('/Question/Basic/getKnowledgeTypes', {subjectid: subjectId,is_gaosi:1}, function(data){
				$('#question-add-knowledge-type, #question-add-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-add-knowledge-type').append('<li onclick="_initCourseType('+subjectId+',' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-add-knowledge-type li:first').click();
			});
		}
	var _initCourseType = function(subjectId,knowledgeTypeId) {
		$.post('/Question/Basic/getCourseTypesBySubjectIdAndKnowledgeTypeId', {subjectid: subjectId, knowledgeTypeId:knowledgeTypeId}, function(data){
			$('#question-add-course-type').html('');
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				$('#question-add-course-type').append('<li onclick="_reRenderStyle(this);_initKnowledge(' + row.id + ');$(\'#course_type_id\').val(\'' + row.id + '\');"><a href="#">' + row.title + '</a></li>');
			});
			if ($('#question-add-course-type li').length == 0) {
				_initKnowledge('');
			}
			else {
				$('#question-add-course-type li:first').click();
			}
		});
	}
	var _initQuestionType = function(subjectId) {
		$.post('/Question/Basic/getQuestionTypesBySubjectId', {subjectid: subjectId}, function(data){
			$('#question-add-question-type').html('');
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				$('#question-add-question-type').append('<li onclick="_reRenderStyle(this);_changeQuestionType(\'' + row.question_type_code + '\');$(\'#question_type_id\').val(\'' + row.id + '\');$(\'#question_type_code\').val(\'' + row.question_type_code + '\');"><a href="#">' + row.title + '</a></li>');
			});
			$('#question-add-question-type li:first').click();
		});
	}
	var _initKnowledge = function(courseTypeId) {
		$('#question-add-knowledge, #question-add-subknowledge').combotree({
			url: '/Question/Basic/getKnowledgesChilds?coursetypeid=' + courseTypeId
		});
	}
	function _changeQuestionType(code) {
		var tmplName = getTmplByQuestionTypeCode(code);
		$.get("/Question/Knowledge/" + tmplName, function(result){
			$('#html').empty().html(result);
			$('.editor').each(function(i ,el) {
				UE.getEditor(el.id);
			});
		});
	}
	var _reRenderStyle = function(o) {
		$(o).addClass('active').siblings().removeClass('active');
	}
	var _initRaty = function() {
		$('#question-add-difficulty').raty({
			number: 3,
			hints: ['容易', '中等', '困难'],
			score: 1
		});
	}
	function check_question(){
		var msg = '';
		if ($('#question-add-knowledge').combotree('getValue') == '') {
			msg += '主知识点不能为空！<br />';
		}
		if ($('#question-add-grades').combobox('getText') == '') {
			msg += '适用年级不能为空！<br />';
		}
		$("div.editor, input.options, input.input").each(function(i,el){
			var id = $(el).attr('id');
			if($('#' + id).val() == ''){
				if (id.indexOf('question_content') == 0) {
					msg += '题干内容不能为空！<br>';
				}
				if (id.indexOf('question_analysis') == 0) {
					msg += '解析内容不能为空！<br>';
				}
				if ((id.indexOf('options') == 0 )) {
					if(id.indexOf('options_content') == 0){
						if(msg.indexOf('答案内容不能为空！<br>')==-1){
							msg += '答案内容不能为空！<br>';
						}
					}else if(id.indexOf('options_answer') == 0){
						if(msg.indexOf('答案选项不能为空！<br>')==-1){
							msg += '答案选项不能为空！<br>';
						}
					}else{
						if(msg.indexOf('选项内容不能为空！<br>')==-1){
							msg += '选项内容不能为空！<br>';
						}
					}	
				}
				if (id.indexOf('answers') == 0) {
					msg += '答案内容不能为空！<br>';
				}
			}
		})
		var flag = 0;
		var is_exist = 0;
		$(':radio.isanswer, :checkbox.isanswer').each(function(i ,el) {
			is_exist = 1;
			var id = $(el).attr('id');
			if (id.indexOf('options_answer_flag') == 0 && $(el).attr('checked') == 'checked') {
				flag = 1;
			}
		});
		if(flag == 0 && is_exist == 1){
			msg += '答案不能为空！<br>';
		}
		if(msg!=''){
			//$.messager.alert('信息提示', '<div style="float: left">'+msg+'</div>', 'warning');return false;
		}else{
		}
		preview_question();
	}
	$(function() {
		_initGrade();
		_initRaty();
		get_question_unique();
		_init();
	});
	function get_question_unique() {
		$.get("/Question/Knowledge/get_question_unique", function(data){
			data = JSON.parse(data);
			uuid = data.uid;
			path = data.path;
			$('#uid').val(uuid);
			$('#sdate').val(path);
		});
	}
	var ctrl = null;
	var uuid = '';
	var path = '';
	function open_word_dialog(ctrlid) {
		var content = '';
		var uid = uuid;
		var euid = '';
		var date = path;
		var params = ctrlid;
		//alert(date + '：' + uid + '\n' +$('#sdate').val() + '：' + $('#uid').val());
		//id
		var strs = ctrlid.split('_');
		if (strs.length > 1)
			euid = strs[1];
		var ctrl = get_ue_ctrl(ctrlid);
		if (ctrl != null)
			content = ctrl.getContent();
		var isExist = '' == content ? 0 : 1;
		//alert(isExist + ' - ' + date + ' - ' + uid + ' - ' + euid);
		UE.getEditor(ctrlid).setDisabled('在WORD中编辑');
		//UE.getEditor('editor').setDisabled('fullscreen');
		var b = new Base64();
		//gs_tiku_plugin().openword(isExist, date, uid, euid, b.encode(params));
		var timestamp=new Date().getTime();
		window.ws.send('openword,'+isExist+',\\\\.\\pipe\\tiku_'+timestamp+','+date+','+uid+','+euid+','+b.encode(params)+',');
	}
	function _init() {
		var wsImpl = window.WebSocket || window.MozWebSocket;
			window.ws = new wsImpl('ws://localhost:12059/');
			var b = new Base64();
			// when data is comming from the server, this metod is called
            ws.onmessage = function (evt) {
                //inc.innerHTML += evt.data + '<br/>'; //调试用
                //收到word发送过来的消息
                msg = evt.data;
                if (msg[0] == '{') {
                    var wm = JSON.parse(msg);
                    var ctrlid = b.decode(wm.otherparams);
                    //alert(wm.data);
                    if (wm["msgName"] == "html"){ //收到html
                        //document.getElementById(ctrlid).innerHTML = wm.data;
                        var ctrl = get_ue_ctrl(ctrlid);
						if (ctrl != null) {
							ctrl.setContent(wm.data, 0);
							ctrl.setEnabled();
							$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
						}
                    }else if (wm["msgName"] == "refresh"){ //html引用的图像上传完成，刷新显示图像
                        //document.getElementById(ctrlid).innerHTML = document.getElementById(wm.eguid).innerHTML;
                        var content = '';
						var ctrl = get_ue_ctrl(ctrlid);
						if (ctrl != null) {
							content = ctrl.getContent();
							ctrl.setContent(content, 0);
							ctrl.setEnabled();
							$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
						}
                    }
                }
            };

            // when the connection is established, this method is called
            ws.onopen = function () {
                //inc.innerHTML += '.. connection open<br/>';
            };

            // when the connection is closed, this method is called
            ws.onclose = function () {
                //inc.innerHTML += '.. connection closed<br/>';
            }
		/*addEvent(gs_tiku_plugin(), 'wordmsg', function (msg) {
			var wm = JSON.parse(msg);
			var b = new Base64();
			var ctrlid = b.decode(wm.otherparams);
			if(wm.msgName == 'html'){
				var ctrl = get_ue_ctrl(ctrlid);
				if (ctrl != null) {
					ctrl.setContent(wm.data, 0);
					ctrl.setEnabled();
					$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
				}
			} else if (wm.msgName == 'refresh') {
				var content = '';
				var ctrl = get_ue_ctrl(ctrlid);
				if (ctrl != null) {
					content = ctrl.getContent();
					ctrl.setContent(content, 0);
					ctrl.setEnabled();
					$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
				}
			} else if (wm.msgName == 'nochange') {
				var ctrl = get_ue_ctrl(ctrlid);
				if (ctrl != null) {
					ctrl.setEnabled();
				}
			}
			else {
				alert('未知返回类型');
			}
		});*/
	}
	</script>
</body>
</html>