<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/umeditor1_2_2/themes/default/_css/umeditor.css" type="text/css" rel="stylesheet">
</head>
<body>
	<div class="easyui-layout" data-options="fit: true">
		<div region="center" data-options="collapsible: false, border: false">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
				<tr>
					<td class="wd_120 alt right">题型：</td>
					<td>
						<ul id="question-add-question-type" class="fliter_box_select"></ul>	
					</td>
				</tr>
			</table>
			<form id="question-add-sub-form" method="post" novalidate>
				<input type="hidden" name="parent_id" value="{$questionid}" />
				<input type="hidden" id="question_type_id" name="question_type_id" value="" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tbody id="html"></tbody>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, split: false, border: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="check_sub_question();">提交</a>
		</div>
	</div>
	<script type="text/javascript" src="/static/umeditor1_2_2/umeditor.config.js"></script>
	<script type="text/javascript" src="/static/umeditor1_2_2/editor_api.js"></script>
	<script type="text/javascript" src="/static/js/question.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min.js"></script>
	<script type="text/javascript">
	function save_sub_question() {
		$('#question-add-sub-form').form('submit', {
			url: '/Question/Knowledge/save_question',
			onSubmit: function () {
				return $(this).form('validate');
			},
			success: function (result) {
				result = JSON.parse(result);
				if (result.status) {
					$.messager.confirm('信息提示', '继续添加子题?', function(r){
						if (!r) {
							parent.$('#question-add-sub-question-dlg').dialog('close');
						}
					});
				}
			}
		});
	}
	var _initQuestionType = function() {
		var subjectId = {$subjectid};
		$.post('/Question/Basic/getQuestionTypesBySubjectId', {subjectid: subjectId}, function(data){
			$('#question-add-question-type').html('');
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				$('#question-add-question-type').append('<li onclick="_reRenderStyle(this);_changeQuestionType(\'' + row.question_type_code + '\');$(\'#question_type_id\').val(\'' + row.id + '\');"><a href="#">' + row.title + '</a></li>');
			});
			$('#question-add-question-type li:first').click();
		});
	}
	var _reRenderStyle = function(o) {
		$(o).addClass('active').siblings().removeClass('active');
	}
	$(function() {
		_initQuestionType();
	});
	function _changeQuestionType(code) {
		var tmplName = getTmplByQuestionTypeCode(code);
		$.get("/Question/Knowledge/" + tmplName, function(result){
			$('#html').empty().html(result);
			$('.editor').each(function(i ,el) {
				var um = UM.getEditor(el.id);
			});
		});
	}


	function check_sub_question(){
		alert(1);
		var msg = '';
		$("textarea.editor, input.options").each(function(i,el){
			var id = $(el).attr('id');
			if($('#'+id).val() == ''){
				if (id.indexOf('content') == 0) {
					msg += '子题题干内容不能为空！<br>';
				}
				if (id.indexOf('analysis') == 0) {
					msg += '子题解析内容不能为空！<br>';
				}
				if ((id.indexOf('options') == 0 )) {
					if(id.indexOf('options_content') == 0){
						if(msg.indexOf('子题答案内容不能为空！<br>')==-1){
							msg += '子题答案内容不能为空！<br>';
						}
					}else if(id.indexOf('options_answer') == 0){
						if(msg.indexOf('子题答案选项不能为空！<br>')==-1){
							msg += '子题答案选项不能为空！<br>';
						}
					}else{
						if(msg.indexOf('子题选项内容不能为空！<br>')==-1){
							msg += '子题选项内容不能为空！<br>';
						}
					}	
				}
				if (id.indexOf('answers') == 0) {
					msg += '子题答案内容不能为空！<br>';
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
			msg += '子题答案不能为空！<br>';
		}
		if(msg!=''){
			$.messager.alert('信息提示', '<div style="float: left">'+msg+'</div>', 'warning');return false;
		}else{
			save_sub_question();
		}
	}
	</script>
</body>
</html>