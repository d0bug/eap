<div id="basic-index-knowledge-layout" class="easyui-layout" data-options="fit: true">
	<div region="north" style="height: 180px;" data-options="title:'请选择属性', iconCls:'icon-search', split: false">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
			<tr>
				<td class="wd_120 alt right">年部：</td>
				<td>
					<ul id="basic-index-knowledge-grade" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict('GRADE_DEPT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('GRADE_DEPT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			<tr>
				<td class="alt right">学科：</td>
				<td>
					<ul id="basic-index-knowledge-subject" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict('SUBJECT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('SUBJECT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			<tr>
				<td class="alt right">教材版本：</td>
				<td>
					<ul id="basic-index-knowledge-type" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict('KNOWLEDGE_TYPE')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('KNOWLEDGE_TYPE')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			<tr>
				<td class="alt right" style="border: 0px">课程类型：</td>
				<td style="border: 0px">
					<ul id="basic-index-knowledge-course-type" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict('COURSE_TYPE')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict('COURSE_TYPE')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
		</table>
		<input type="hidden" id="basic_index_knowledge_grade_id" value="">
		<input type="hidden" id="basic_index_knowledge_subject_id" value="">
		<input type="hidden" id="basic_index_knowledge_type_id" value="">
		<input type="hidden" id="basic_index_knowledge_course_type_id" value="">
	</div>
	<div region="center" data-options="title:'知识点', iconCls:'icon-table', split: true">
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
					
					<th field="name" width="500" formatter="tree_node_formatter1">名称</th>
					<th field="remark" width="300">描述</th>
					<th field="sort" width="50">排序</th>
				</tr>
			</thead>
		</table>
		<div id="basic-index-knowledge-treegrid-toolbar">
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_knowledge()">添加</a>
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_knowledge()">编辑</a>
			<a href="#" class="easyui-linkbutton" id="basic-index-knowledge-match" style="float:left" data-options="plain:true, iconCls:'icon-add',disabled:true" onclick="javascript: match_knowledge()">匹配五级知识点</a>
			<div class="datagrid-btn-separator" style="float:left" />
			<a href="#" class="easyui-linkbutton" id="basic-index-knowledge-questions" style="float:left" data-options="plain:true, iconCls:'icon-table',disabled:true" onclick="javascript: view_knowledge_questions()">查看试题</a>
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-search'" onclick="javascript: only_4_5_level_knowledge()">只显示四、五级</a>
		</div>
	</div>
</div>
<!--Begin 知识点添加/编辑对话框-->
<div id="basic-index-knowledge-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:520px;padding:5px;">
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
<script language='javascript' type='text/javascript'>
	function knowledge_select(row) {
		$('#basic-index-knowledge-questions').linkbutton({
			disabled: row.is_leaf == '1' ? false : true
		});
		$('#basic-index-knowledge-match').linkbutton({
			disabled: (row.level == 4 && $('#basic_index_knowledge_type_id').val()!=1) ? false : true
		});
	}
	function only_4_5_level_knowledge() {
		var courseTypeId = $('#basic_index_knowledge_course_type_id').val();
		var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
		var $grid = $('#basic-index-knowledge-treegrid');
		var params = $grid.treegrid('options').queryParams;
		params.id = '';
		params.coursetypeid = courseTypeId;
		params.knowledgetypeid = knowledgeTypeId;

		$grid.treegrid({url: '/Question/Basic/getKnowledges2'});
	}
	function tree_node_formatter1(val, row) {
		if (row.is_leaf == 1) {
			return row.name + '(<span class="red">' + row.knode_classic_question_num + '</span>/<span class="red">' + row.knode_question_num + '</span>)';
		}
		return row.name;
	}
	function view_knowledge_questions() {
		var row = $('#basic-index-knowledge-treegrid').treegrid('getSelected');
		if (row) {
			$('#basic-index-knowledge-questions-dlg').dialog({
				href: '/Question/basic/view_knowledge_questions?knowledge_id=' + row.id
			}).dialog('open');
		} else {
			$.messager.alert('提示信息', '请选择知识点！', 'info');
		}
	}
	var action, form;
	function add_knowledge() {
		var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
        if (knowledgeTypeId == '') {
			$.messager.alert('提示信息', '请选择知识点版本!', 'info');
			return;
		}
		var courseTypeId = $('#basic_index_knowledge_course_type_id').val();
        if (courseTypeId == '') {
			$.messager.alert('提示信息', '请选择课程类型!', 'info');
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
		$('#basic-index-knowledge-dlg-iframe')[0].src = '/Question/Basic/knowledge_add?pid=' + id + '&coursetypeid=' + courseTypeId + '&knowledgetypeid=' + knowledgeTypeId + '&level=' + level;
		
		$('#basic-index-knowledge-dlg').dialog(params).dialog('open');
	}
	function edit_knowledge() {
		var row = $('#basic-index-knowledge-treegrid').treegrid('getSelected');
		if (row) {
			var params = {
				iconCls: 'icon-edit',
				title:'编辑',
			};
			var courseTypeId = $('#basic_index_knowledge_course_type_id').val();
			$('#basic-index-knowledge-dlg-iframe')[0].src = '/Question/Basic/knowledge_edit?id=' + row.id + '&coursetypeid=' + courseTypeId;
			$('#basic-index-knowledge-dlg').dialog(params).dialog('open');
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
				_initKnowledge('');
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
							_initCourseType(subjectId,row.id);
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
				_initCourseType('');
			}
		});
	}
	var _initCourseType = function(subjectId,knowledgeTypeId) {
		if(knowledgeTypeId==''){
			var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
		}
		$.post('/Question/Basic/getCourseTypesBySubjectIdAndKnowledgeTypeId', {subjectid: subjectId, knowledgeTypeId:knowledgeTypeId}, function(data){
			$('#basic-index-knowledge-course-type').html('');
			
			var courseTypeId = $('#basic_index_knowledge_course_type_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_knowledge_course_type_id').val(row.id);
							_initKnowledge(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-knowledge-course-type').append($li);
					if (courseTypeId == row.id) {
						index = i;
					}
				});
				$('#basic-index-knowledge-course-type li').eq(index).click();
			}
			else {
				$('#basic_index_knowledge_course_type_id').val('');
				_initKnowledge('');
			}
		});
	}
	function _initKnowledge(courseTypeId) {
		var $grid = $('#basic-index-knowledge-treegrid');
		var params = $grid.treegrid('options').queryParams;
		params.id = '';
		params.coursetypeid = courseTypeId;
		//params.knowledgetypeid = $('#basic_index_knowledge_type_id').val();
		$grid.treegrid({
						url: '/Question/Basic/getKnowledges1',
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
	function add_dict(type){
		var params = {
				href: '/Question/Basic/add?cate=' + type + '&gid=' + $('#basic_index_knowledge_grade_id').val() + '&sid=' + $('#basic_index_knowledge_subject_id').val()+ '&ktid=' + $('#basic_index_knowledge_type_id').val() + '&cid=' + $('#coursetypeid').val(),
				iconCls: 'icon-add',
				title: '添加'
			};
		if (type == 'GRADE_DEPT') {
			params.height = 155;
		}
		if(type == 'SUBJECT') {
			if ($('#basic_index_knowledge_grade_id').val() == '') {
				$.messager.alert('错误信息', '请先选择年部!', 'error');
				return false;
			}
			params.height = 192;
		}
		if(type == 'KNOWLEDGE_TYPE') {
			if ($('#basic_index_knowledge_grade_id').val() == '' || $('#basic_index_knowledge_subject_id').val() == '') {
				$.messager.alert('错误信息', '请先选择年部、学科!', 'error');
				return false;
			}
			params.height = 229;
		}
		if(type == 'COURSE_TYPE') {
			if(($('#basic_index_knowledge_grade_id').val() == '' || $('#basic_index_knowledge_subject_id').val() == ''|| $('#basic_index_knowledge_type_id').val() == '')) {
				$.messager.alert('错误信息', '请先选择年部、学科、教材版本!', 'error');
				return false;
			}
			params.height = 262;
		}
		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
		action = '/Question/Basic/add_save';
		form = 'dict-add-form';
	}
	function edit_dict(type) {
		var params = {
				height: 155,
				iconCls: 'icon-edit',
				title: '编辑'
			};
		var id = '';
		if(type == 'GRADE_DEPT') {
			id = $('#basic_index_knowledge_grade_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择年部!', 'error');
				return false;
			}
		}
		if(type=='SUBJECT') {
			id = $('#basic_index_knowledge_subject_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择学科!', 'error');
				return false;
			}
		}
		if(type=='KNOWLEDGE_TYPE') {
			id = $('#basic_index_knowledge_type_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择教材版本!', 'error');
				return false;
			}
		}
		if(type == 'COURSE_TYPE') {
			id = $('#basic_index_knowledge_course_type_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择课程类型!', 'error');
				return false;
			}
		}
		params.href = '/Question/Basic/edit?cate=' + type + '&id=' + id;
		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
		action = '/Question/Basic/edit_save';
		form = 'dict-edit-form';
	}
	function dict_save() {
		$('#' + form).form('submit', {
	        url: action,
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	$('#basic-index-knowledge-add-dlg').dialog('close');
	                _initGrade();
	            } else {
	                $.messager.alert('错误信息', '操作失败!', 'error');
	            }
	        }
	    });
    }
    
    //匹配五级知识点
    function match_knowledge(){
    	var knowledgeTypeId = $('#basic_index_knowledge_type_id').val();
        if (knowledgeTypeId == '') {
			$.messager.alert('提示信息', '请选择知识点版本!', 'info');
			return;
		}
		var courseTypeId = $('#basic_index_knowledge_course_type_id').val();
        if (courseTypeId == '') {
			$.messager.alert('提示信息', '请选择课程类型!', 'info');
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
			title: '匹配知识点'
		};
		level = parseInt(level) + 1;
		$('#basic-index-knowledge-match-dlg-iframe')[0].src = '/Question/Basic/knowledge_match?pid=' + id + '&coursetypeid=' + courseTypeId + '&knowledgetypeid=' + knowledgeTypeId + '&level=' + level;
		
		$('#basic-index-knowledge-match-dlg').dialog(params).dialog('open');
    }
</script>