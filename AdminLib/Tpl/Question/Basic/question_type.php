<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/easyui/treegrid-dnd.js"></script>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
	<div id="basic-index-question-layout" class="easyui-layout" fit="true">
		<div region="north" data-options="title:'条件选择', iconCls:'icon-search', fit: true, split: false, collapsible:false">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
			<tr>
				<td class="wd_120 alt right">年部：</td>
				<td>
					<ul id="basic-index-question-grade" class="fliter_box_select"></ul>
				</td>
			</tr>
			<tr>
				<td class="alt right">学科：</td>
				<td>
					<ul id="basic-index-question-subject" class="fliter_box_select"></ul>
				</td>
			</tr>
			<tr>
				<td class="alt right">题型：</td>
				<td>
					<ul id="basic-index-question-question-type" class="fliter_box_select"></ul>
					<a href="#" onclick="add_question_type()" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_question_type()" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			</table>
			<input type="hidden" id="basic_index_question_type_grade_id" value="">
			<input type="hidden" id="basic_index_question_type_subject_id" value="">
			<input type="hidden" id="basic_index_question_type_id" value="">
		</div>
	</div>
	<!--Begin 基础属性添加对话框-->
	<div id="basic-index-question-type-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true" style="padding:5px;"></div>
	<!--End 基础属性添加对话框-->
	<!--Begin 基础属性编辑对话框-->
	<div id="basic-index-question-type-edit-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-question-type-edit-dlg-buttons'" style="width:450px;height:155px;padding:5px;"></div>
	<div id="basic-index-question-type-edit-dlg-buttons">
		<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="question_type_save()">保存</a>
	</div>
	<!--End 基础属性编辑对话框-->
	<script language='javascript' type='text/javascript'>
	var _initGrade1 = function() {
		$.post('/Question/Basic/getGrades', null, function(data) {
			$('#basic-index-question-grade').html('');
			$('#basic-index-question-subject').html('');
			$('#basic-index-question-question-type').html('');
			
			var gradeId = $('#basic_index_question_type_grade_id').val();
			var index = 0;
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				var $li = $('<li></li>');
				$li.attr('value', row.id)
					.click(function() {
						$(this).addClass('active').siblings().removeClass('active');
						$('#basic_index_question_type_grade_id').val(row.id);
						_initSubject1(row.id);
					})
					.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
				$('#basic-index-question-grade').append($li);
				if (gradeId == row.id) {
					index = i;
				}
			});
			$('#basic-index-question-grade li').eq(index).click();
		});
	}
	var _initSubject1 = function(gradeId) {
		$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data) {
			$('#basic-index-question-subject').html('');
			$('#basic-index-question-question-type').html('');
			
			var subjectId = $('#basic_index_question_type_subject_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_question_type_subject_id').val(row.id);
							_initQuestionType(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-question-subject').append($li);
					if (subjectId == row.id) {
						index = i;
					}
				});
				$('#basic-index-question-subject li').eq(index).click();
			}
			else {
				$('#basic_index_question_type_subject_id').val('');
				$('#basic_index_question_type_id').val('');
			}
		});
	}
	var _initQuestionType = function(subjectId) {
		$.post('/Question/Basic/getQuestionTypesBySubjectId', {subjectid: subjectId}, function(data){
			$('#basic-index-question-question-type').html('');
			
			var questionTypeId = $('#basic_index_question_type_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_question_type_id').val(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-question-question-type').append($li);
					if (questionTypeId == row.id) {
						index = i;
					}
				});
				$('#basic-index-question-question-type li').eq(index).click();
			}
			else {
				$('#basic_index_question_type_id').val('');
			}
		});
	}
	$(function() {
		_initGrade1();
	});

	function add_question_type(type){
		$('#basic-index-question-type-add-dlg').dialog({
			href: '/Question/Basic/question_type_add?sid=' + $('#basic_index_question_type_subject_id').val(),
			iconCls: 'icon-add',
			title: '添加',
			width: 650,
			height: 355
		}).dialog('open');
		action = '/Question/Basic/question_type_add_save';
		form = 'questiontype-add-form';
	}
	function edit_question_type() {
		id = $('#basic_index_question_type_id').val();
		if(id == '') {
			$.messager.alert('错误信息', '请选择题型!', 'error');
			return false;
		}
		$('#basic-index-question-type-edit-dlg').dialog({
			href: '/Question/Basic/question_type_edit?id=' + id,
			iconCls: 'icon-edit',
			title: '编辑'
		}).dialog('open');
		action = '/Question/Basic/question_type_edit_save';
		form = 'questiontype-edit-form';
	}
	function question_type_save() {
		$('#' + form).form('submit', {
	        url: action,
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	_initGrade1();
					$('#basic-index-question-type-add-dlg, #basic-index-question-type-edit-dlg').dialog('close');
	            } else {
	                $.messager.alert('错误信息', '操作失败!', 'error');
	            }
	        }
	    });
    }
</script>
</body>
</html>