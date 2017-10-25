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
<div region="center" data-options="collapsible: false, border: false">
	<form id="question-edit-form" method="post" novalidate>
		<input type="hidden" id="id" name="id" value="{$question['id']}" />
		<input type="hidden" id="code" name="code" value="{$code}" />
		<input type="hidden" id="course_type_id" name="course_type_id" value="{$question['course_type_id']}" />
		<input type="hidden" id="question_type_id" name="question_type_id" value="{$question['question_type_id']}" />	
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tbody id="html"></tbody>
			</div>
		</table>
	</form>
</div>
<div id="edit-sub-question-dlg-win" region="south" style="height: 32px;" data-options="collapsible: false, split: false, border: false">
	<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="edit_save_question()">提交</a>
</div>
<script type="text/javascript" src="/static/umeditor1_2_2/umeditor.config.js"></script>
<script type="text/javascript" src="/static/umeditor1_2_2/editor_api.js"></script>
<script type="text/javascript" src="/static/js/question.js"></script>
<script type="text/javascript">
$(function() {
	var id = '{$question["id"]}';
	var code ='{$code}';
	_changeSubQuestionType(code,id);
});
function _changeSubQuestionType(code,id) {
	var tmplName = getTmplByQuestionTypeCode(code);
	$.get("/Question/Knowledge/" + tmplName , {id: id }, function(result){
		$('#html').empty().html(result);
		$('.editor').each(function(i ,el) {
			var um = UM.getEditor(el.id);
		});
	});
}	

function edit_save_question(){
	$('#question-edit-form').form('submit', {
		url: '/Question/Knowledge/edit_save_question',
		onSubmit: function () {
			return $(this).form('validate');
		},
		success: function (result) {
			result = JSON.parse(result);
			$('#edit-sub-question-dlg-win').window.close();	
			if(result.status){
				$.messager.alert('提示信息', '修改成功!', 'info');
						
			}else{
				$.messager.alert('提示信息', '修改失败!', 'info');
			}
			
		}
	});
}
</script>
</body>
</html>