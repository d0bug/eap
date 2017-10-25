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
	<div id="basic-index-knowledge-layout" class="easyui-layout" data-options="fit: true">
	<div region="north" style="height: 140px;" data-options="title:'请选择属性', iconCls:'icon-search', split: false">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
			<tr>
				<td class="wd_120 alt right">年部：</td>
				<td>
					<ul id="basic-index-knowledge-grade" class="fliter_box_select"></ul>
					<!--<a href="#" onclick="add_dict('GRADE_DEPT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('GRADE_DEPT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>-->
				</td>
			</tr>
			<tr>
				<td class="alt right">学科：</td>
				<td>
					<ul id="basic-index-knowledge-subject" class="fliter_box_select"></ul>
					<!--<a href="#" onclick="add_dict('SUBJECT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('SUBJECT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>-->
				</td>
			</tr>
			<tr>
				<td class="alt right">教材版本：</td>
				<td>
					<ul id="basic-index-knowledge-type" class="fliter_box_select"></ul>
					<!--<a href="#" onclick="add_dict('KNOWLEDGE_TYPE')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('KNOWLEDGE_TYPE')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>-->
				</td>
			</tr>
		</table>
		<input type="hidden" id="basic_index_knowledge_grade_id" value="">
		<input type="hidden" id="basic_index_knowledge_subject_id" value="">
		<input type="hidden" id="basic_index_knowledge_type_id" value="">
	</div>
	<div region="center" data-options="title:'课程体系4.0', iconCls:'icon-table', split: true">
		<table id="basic-index-knowledge-treegrid" class="easyui-treegrid" data-options="
																  iconCls: 'icon-table',
																  striped: true,
																  toolbar: '#basic-index-knowledge-treegrid-toolbar',
																  border: false,
																  fit: true,
																  idField: 'id',
																  treeField: 'name',
																  rownumbers: true,
																  onSelect: knowledge_select">
			<thead>
				<tr>
					
					<th field="name" width="500">名称</th>
					<th field="sort" width="50">排序</th>
				</tr>
			</thead>
		</table>
		<div id="basic-index-knowledge-treegrid-toolbar">
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_course()">添加</a>
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_course()">编辑</a>
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: delete_course()">删除</a>
		</div>
	</div>
</div>
<!--Begin 知识点添加/编辑对话框-->
<div id="basic-index-knowledge-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:250px;padding:5px;">
    <iframe scrolling="auto" id="basic-index-knowledge-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 知识点添加/编辑对话框-->
<!--Begin 基础属性添加/编辑对话框-->
<div id="basic-index-knowledge-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-knowledge-add-dlg-buttons'" style="width:500px;height:235px;padding:5px;"></div>
<div id="basic-index-knowledge-add-dlg-buttons">
	<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="dict_save()">保存</a>
</div>
<!--End 基础属性添加/编辑对话框-->
<!--Begin 查看试题对话框-->
<div id="basic-index-knowledge-questions-dlg" class="easyui-dialog" data-options="modal:true,closed:true" title="查看试题" style="width:950px;height:500px;padding:5px;"></div>
<!--End 查看试题对话框-->
<!--Begin 匹配五级知识点对话框-->
<div id="basic-index-knowledge-match-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:420px;padding:5px;">
    <iframe scrolling="auto" id="basic-index-knowledge-match-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 匹配五级知识点对话框-->

</body>
</html>
<script language='javascript' type='text/javascript'>
	function knowledge_select(row) {
		$('#basic-index-knowledge-questions').linkbutton({
			disabled: row.is_leaf == '1' ? false : true
		});
		$('#basic-index-knowledge-match').linkbutton({
			disabled: (row.level == 4 && $('#basic_index_knowledge_type_id').val()!=1) ? false : true
		});
	}
	
	/*function tree_node_formatter1(val, row) {
		if (row.is_leaf == 1) {
			return row.name + '(<span class="red">' + row.knode_classic_question_num + '</span>/<span class="red">' + row.knode_question_num + '</span>)';
		}
		return row.name;
	}*/
	
	var action, form;
	function add_course() {
		var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
        if (knowledgeTypeId == '') {
			$.messager.alert('提示信息', '请选择知识点版本!', 'info');
			return;
		}
		
		var id = '';
		var level = 0;
		var row = $('#basic-index-knowledge-treegrid').treegrid('getSelected');
		if (row) {
			id = row.id;
			level = row.level;
		}
		var params = {
			iconCls: 'icon-add',
			title: '添加'
		};
		level = parseInt(level) + 1;
		$('#basic-index-knowledge-dlg-iframe')[0].src = '/Vip/VipJiaoyan/course_add?pid=' + id + '&knowledgetypeid=' + knowledgeTypeId + '&level=' + level;
		
		$('#basic-index-knowledge-dlg').dialog(params).dialog('open');
	}
	function edit_course() {
		var row = $('#basic-index-knowledge-treegrid').treegrid('getSelected');
		if (row) {
			var params = {
				iconCls: 'icon-edit',
				title:'编辑',
			};
			var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
			$('#basic-index-knowledge-dlg-iframe')[0].src = '/Vip/VipJiaoyan/course_edit?id=' + row.id+'&knowledgetypeid='+ knowledgeTypeId;
			$('#basic-index-knowledge-dlg').dialog(params).dialog('open');
		} else {
			$.messager.alert('提示信息', '请选择要操作的数据！', 'info');
		}
	}
	
	function delete_course(){
		var row = $('#basic-index-knowledge-treegrid').treegrid('getSelected');
		if (row) {
			var params = {
				iconCls: 'icon-remove',
				title:'删除',
			};
			var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
			$.post('/Vip/VipJiaoyan/course_delete', 
				  {id: row.id,knowledgetypeid:knowledgeTypeId}, 
				  function(data) {
				  	data = JSON.parse(data);
				  	if(data.status == 1){
				  		$.messager.alert('提示信息', '删除成功！', 'info');
				  		_initCourse(knowledgeTypeId);
				  	}else{
				  		$.messager.alert('提示信息', '删除失败！', 'info');
				  	}
						
				  })
		} else {
			$.messager.alert('提示信息', '请选择要操作的数据！', 'info');
		}
	}
	var _initGrade = function() {
		$.post('/Question/Basic/getGrades', null, function(data) {
			$('#basic-index-knowledge-grade').html('');
			$('#basic-index-knowledge-subject').html('');
			$('#basic-index-knowledge-type').html('');
			$('#basic-index-knowledge-course-type').html('');
			
			var gradeId = $('#basic_index_knowledge_grade_id').val();
			var index = 0;
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				var $li = $('<li></li>');
				$li.attr('value', row.id)
					.click(function() {
						$(this).addClass('active').siblings().removeClass('active');
						$('#basic_index_knowledge_grade_id').val(row.id);
						_initSubject(row.id);
						_initKnowledgeType(row.id);
					})
					.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
				$('#basic-index-knowledge-grade').append($li);
				if (gradeId == row.id) {
					index = i;
				}
			});
			$('#basic-index-knowledge-grade li').eq(index).click();
		});
	}
	var _initSubject = function(gradeId) {
		$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data) {
			$('#basic-index-knowledge-subject').html('');
			$('#basic-index-knowledge-type').html('');
			$('#basic-index-knowledge-course-type').html('');
			
			var subjectId = $('#basic_index_knowledge_subject_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_knowledge_subject_id').val(row.id);
							//_initCourseType(row.id);
							_initKnowledgeType(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-knowledge-subject').append($li);
					if (subjectId == row.id) {
						index = i;
					}
				});
				$('#basic-index-knowledge-subject li').eq(index).click();
			}
			else {
				$('#basic_index_knowledge_subject_id').val('');
				$('#basic_index_knowledge_course_type_id').val('');
				_initCourse('');
			}
		});
	}
	var _initKnowledgeType = function(subjectId) {
		$.post('/Question/Basic/getKnowledgeTypes', {subjectid:subjectId}, function(data){
			$('#basic-index-knowledge-type').html('');
			var subjectId = $('#basic_index_knowledge_subject_id').val();
			var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_knowledge_type_id').val(row.id);
							_initCourse(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-knowledge-type').append($li);
					if (knowledgeTypeId == row.id) {
						index = i;
					}
				});
				$('#basic-index-knowledge-type li').eq(index).click();
			}
			else {
				$('#basic_index_knowledge_type_id').val('');
				_initCourse('');
			}
		});
	}
	
	function _initCourse(knowledgeTypeId) {
		var $grid = $('#basic-index-knowledge-treegrid');
		var params = $grid.treegrid('options').queryParams;
		params.id = '';
		params.knowledgetypeid = knowledgeTypeId;
		$grid.treegrid({
						url: '/Vip/VipJiaoyan/getCourses1',
						onClickRow:function(row){
						},
						onExpand:function(row){
							//alert(row.id);
							//alert(row.level);
						}
		});
	}
	$(function() {
		_initGrade();
	});
	
</script>