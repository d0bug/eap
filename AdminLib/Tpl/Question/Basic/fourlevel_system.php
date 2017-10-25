<div id="basic-index-knowledge-layout" class="easyui-layout" data-options="fit: true">
	<div region="north" style="height: 120px;" data-options="title:'请选择属性', iconCls:'icon-search', split: false">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
			<tr>
				<td class="wd_120 alt right">年部：</td>
				<td>
					<ul id="basic-index-fourlevel-grade" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict2('GRADE_DEPT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict2('GRADE_DEPT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			<tr>
				<td class="alt right">学科：</td>
				<td>
					<ul id="basic-index-fourlevel-subject" class="fliter_box_select"></ul>
					<a href="#" onclick="add_dict2('SUBJECT')" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_dict2('SUBJECT')" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
		</table>
		<input type="hidden" id="basic_index_fourlevel_grade_id" value="">
		<input type="hidden" id="basic_index_fourlevel_subject_id" value="">
	</div>
	<div region="center" data-options="title:'体系', iconCls:'icon-table', split: true">
		<table id="basic-index-fourlevel-treegrid" class="easyui-treegrid" data-options="
																  iconCls: 'icon-table',
																  striped: true,
																  toolbar: '#basic-index-fourlevel-treegrid-toolbar',
																  border: false,
																  fit: true,
																  idField: 'id',
																  treeField: 'name',
																  rownumbers: true,
																  onSelect: knowledge_select2">
			<thead>
				<tr>
					
					<th field="name" width="500" formatter="tree_node_formatter1">名称</th>
					<th field="remark" width="300">描述</th>
					<th field="sort" width="50">排序</th>
				</tr>
			</thead>
		</table>
		<div id="basic-index-fourlevel-treegrid-toolbar">
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_fourlevel()">添加</a>
			<a href="#" class="easyui-linkbutton" style="float:left" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_fourlevel()">编辑</a>
			<a href="#" class="easyui-linkbutton" id="basic-index-fourlevel-match" style="float:left" data-options="plain:true, iconCls:'icon-add',disabled:true" >匹配五级知识点</a>
			<div class="datagrid-btn-separator" style="float:left" />
			<a href="#" class="easyui-linkbutton" id="basic-index-fourlevel-questions" style="float:left" data-options="plain:true, iconCls:'icon-table',disabled:true" onclick="javascript: view_fourlevel_questions()">查看试题</a>
			<a href="#" class="easyui-linkbutton"   data-options="plain:true, iconCls:'icon-search'" onclick="javascript: only_4_5_level_fourlevel()">只显示四、五级</a>
			<a href="#" class="easyui-linkbutton" id="basic-index-fourlevel-delete" style="float:left" data-options="plain:true, iconCls:'icon-remove',disabled:true" onclick="javascript: delete_fourlevel()">删除</a>
            
		</div>
	</div>
</div>
<!--Begin 知识点添加/编辑对话框-->
<div id="basic-index-fourlevel-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:520px;padding:5px;">
    <iframe scrolling="auto" id="basic-index-fourlevel-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 知识点添加/编辑对话框-->
<!--Begin 基础属性添加/编辑对话框-->
<div id="basic-index-fourlevel-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-fourlevel-add-dlg-buttons'" style="width:500px;height:235px;padding:5px;"></div>
<div id="basic-index-fourlevel-add-dlg-buttons">
	<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="dict_save2()">保存</a>
</div>
<!--End 基础属性添加/编辑对话框-->
<!--Begin 查看试题对话框-->
<div id="basic-index-fourlevel-questions-dlg" class="easyui-dialog" data-options="modal:true,closed:true" title="查看试题" style="width:950px;height:500px;padding:5px;"></div>
<!--End 查看试题对话框-->
<!--Begin 匹配五级知识点对话框-->
<div id="basic-index-fourlevel-match-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:550px;padding:5px;">
    <iframe scrolling="auto" id="basic-index-fourlevel-match-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 匹配五级知识点对话框-->
<script language='javascript' type='text/javascript'>
	function knowledge_select2(row) {
		$('#basic-index-fourlevel-questions').linkbutton({
			disabled: row.is_leaf == '1' ? false : true
		});
		$('#basic-index-fourlevel-match').linkbutton({
			disabled: (row.level == 4) ? false : true
		});
		$('#basic-index-fourlevel-delete').linkbutton({
			disabled: (row.level == 4||row.level == 5) ? false : true
		});
		if(row.level == 4){
			$('#basic-index-fourlevel-match').tooltip('destroy');
			$('#basic-index-fourlevel-match').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
					if (row) {
						id = row.id;
						level = row.level;
						level = parseInt(level) + 1;
					};
					content.panel({
						width: 555,
						height: 500,
						border: false,
						cache: false,
						href: '/Question/Basic/fourlevels3?subjectid='+$('#basic_index_fourlevel_subject_id').val()+'&is_gaosi=1&pid='+id+'&level='+level,
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});
		}
		
	}
	function only_4_5_level_fourlevel() {
		var subjectId = $('#basic_index_fourlevel_subject_id').val();
		var $grid = $('#basic-index-fourlevel-treegrid');
		var params = $grid.treegrid('options').queryParams;
		params.id = '';
		params.subjectid = subjectId;
		$grid.treegrid({url: '/Question/Basic/getFourlevels2'});
	}
	function tree_node_formatter1(val, row) {
		if (row.is_leaf == 1) {
			return row.name + '(<span class="red">' + row.knode_classic_question_num + '</span>/<span class="red">' + row.knode_question_num + '</span>)';
		}
		return row.name;
	}
	function view_fourlevel_questions() {
		var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
		if (row) {
			$('#basic-index-fourlevel-questions-dlg').dialog({
				href: '/Question/basic/view_fourlevel_questions?fourlevel_id=' + row.id
			}).dialog('open');
		} else {
			$.messager.alert('提示信息', '请选择体系！', 'info');
		}
	}
	var action, form;
	function add_fourlevel() {
		var gradeId = $('#basic_index_fourlevel_grade_id').val();
        if (gradeId == '') {
			$.messager.alert('提示信息', '请选择年部!', 'info');
			return;
		}
		var subjectId = $('#basic_index_fourlevel_subject_id').val();
        if (subjectId == '') {
			$.messager.alert('提示信息', '请选择学科!', 'info');
			return;
		}
		
		
		var id = '';
		var level = 0;
		var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
		if (row) {
			id = row.id;
			level = row.level;
		}
		var params = {
			iconCls: 'icon-add',
			title: '添加'
		};
		level = parseInt(level) + 1;
		$('#basic-index-fourlevel-dlg-iframe')[0].src = '/Question/Basic/fourlevel_add?pid=' + id + '&subjectid=' + subjectId  + '&level=' + level;
		
		$('#basic-index-fourlevel-dlg').dialog(params).dialog('open');
	}
	function edit_fourlevel() {
		var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
		if (row) {
			var params = {
				iconCls: 'icon-edit',
				title:'编辑',
			};
			var subjectId = $('#basic_index_fourlevel_subject_id').val();
			$('#basic-index-fourlevel-dlg-iframe')[0].src = '/Question/Basic/fourlevel_edit?id=' + row.id + '&subjectid=' + subjectId;
			$('#basic-index-fourlevel-dlg').dialog(params).dialog('open');
		} else {
			$.messager.alert('提示信息', '请选择要操作的数据！', 'info');
		}
	}
	
	function delete_fourlevel(){
		var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
		if (row) {
			if(row.id){
				$.post('/Question/Basic/fourlevel_delete', {id:row.id,level:row.level}, function(data) {
					var data = JSON.parse(data);
					if(data.status == 1){
						alert('删除成功');
						_initFourlevel($('#basic_index_fourlevel_subject_id').val());
					}else{
						alert('删除失败');
					}
				});
			}
			
		} else {
			$.messager.alert('提示信息', '请选择要操作的数据！', 'info');
		}
	}
	var _initGrade2 = function() {
		$.post('/Question/Basic/getGrades', null, function(data) {
			$('#basic-index-fourlevel-grade').html('');
			$('#basic-index-fourlevel-subject').html('');
			var gradeId = $('#basic_index_fourlevel_grade_id').val();
			var index = 0;
			data = JSON.parse(data);
			$.each(data, function(i, row) {
				var $li = $('<li></li>');
				$li.attr('value', row.id)
					.click(function() {
						$(this).addClass('active').siblings().removeClass('active');
						$('#basic_index_fourlevel_grade_id').val(row.id);
						_initSubject2(row.id);
					})
					.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
				$('#basic-index-fourlevel-grade').append($li);
				if (gradeId == row.id) {
					index = i;
				}
			});
			$('#basic-index-fourlevel-grade li').eq(index).click();
		});
	}
	var _initSubject2 = function(gradeId) {
		$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data) {
			$('#basic-index-fourlevel-subject').html('');
			var subjectId = $('#basic_index_fourlevel_subject_id').val();
			var index = 0;
			data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_fourlevel_subject_id').val(row.id);
							_initFourlevel(row.id);
						})
						.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
					$('#basic-index-fourlevel-subject').append($li);
					if (subjectId == row.id) {
						index = i;
					}
				});
				$('#basic-index-fourlevel-subject li').eq(index).click();
			}
			else {
				$('#basic_index_fourlevel_subject_id').val('');
				_initFourlevel('');
			}
		});
	}
	
	function _initFourlevel(subjectId) {
		var $grid = $('#basic-index-fourlevel-treegrid');
		var params = $grid.treegrid('options').queryParams;
		params.id = '';
		params.subjectid = subjectId;
		$grid.treegrid({
						url: '/Question/Basic/getFourlevels',
						onClickRow:function(row){
						},
						onExpand:function(row){
							//alert(row.id);
							//alert(row.level);
						}
		});
	}
	$(function() {
		_initGrade2();
		/*$('#basic-index-fourlevel-match').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');alert(row.id);
					if (row) {
						id = row.id;
						level = row.level;
						level = parseInt(level) + 1;
					};
					content.panel({
						width: 555,
						height: 500,
						border: false,
						cache: false,
						href: '/Question/Basic/fourlevels3?subjectid='+$('#basic_index_fourlevel_subject_id').val()+'&is_gaosi=1&pid='+id+'&level='+level,
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});*/
	});
	function add_dict2(type){
		var params = {
				href: '/Question/Basic/add?cate=' + type + '&gid=' + $('#basic_index_fourlevel_grade_id').val() + '&sid=' + $('#basic_index_fourlevel_subject_id').val(),
				iconCls: 'icon-add',
				title: '添加'
			};
		if (type == 'GRADE_DEPT') {
			params.height = 155;
		}
		if(type == 'SUBJECT') {
			if ($('#basic_index_fourlevel_grade_id').val() == '') {
				$.messager.alert('错误信息', '请先选择年部!', 'error');
				return false;
			}
			params.height = 192;
		}
		$('#basic-index-fourlevel-add-dlg').dialog(params).dialog('open');
		action = '/Question/Basic/add_save';
		form = 'dict-add-form';
	}
	function edit_dict2(type) {
		var params = {
				height: 155,
				iconCls: 'icon-edit',
				title: '编辑'
			};
		var id = '';
		if(type == 'GRADE_DEPT') {
			id = $('#basic_index_fourlevel_grade_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择年部!', 'error');
				return false;
			}
		}
		if(type=='SUBJECT') {
			id = $('#basic_index_fourlevel_subject_id').val();
			if(id == '') {
				$.messager.alert('错误信息', '请选择学科!', 'error');
				return false;
			}
		}
		params.href = '/Question/Basic/edit?cate=' + type + '&id=' + id;
		$('#basic-index-fourlevel-add-dlg').dialog(params).dialog('open');
		action = '/Question/Basic/edit_save';
		form = 'dict-edit-form';
	}
	function dict_save2() {
		$('#' + form).form('submit', {
	        url: action,
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	$('#basic-index-fourlevel-add-dlg').dialog('close');
	                _initGrade2();
	            } else {
	                $.messager.alert('错误信息', '操作失败!', 'error');
	            }
	        }
	    });
    }
    
    //匹配五级知识点
    function match_knowledge2(){
    	var subjectId = $('#basic_index_fourlevel_subject_id').val();
        if (subjectId == '') {
			$.messager.alert('提示信息', '请选择学科!', 'info');
			return;
		}
		
		var id = '';
		var level = 0;
		var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');
		if (row) {
			id = row.id;
			level = row.level;
		}
		var params = {
			iconCls: 'icon-add',
			title: '匹配知识点'
		};
		level = parseInt(level) + 1;
		$('#basic-index-fourlevel-match-dlg-iframe')[0].src = '/Question/Basic/fourlevel_match?pid=' + id + '&subjectid=' + subjectId + '&level=' + level;
		
		$('#basic-index-fourlevel-match-dlg').dialog(params).dialog('open');
    }
    
    
    function fourlevel_match(){
    	$('#basic-index-fourlevel-match').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					var row = $('#basic-index-fourlevel-treegrid').treegrid('getSelected');alert(row.id);
					if (row) {
						id = row.id;
						level = row.level;
						level = parseInt(level) + 1;
					};
					content.panel({
						width: 555,
						height: 500,
						border: false,
						cache: false,
						href: '/Question/Basic/fourlevels3?subjectid='+$('#basic_index_fourlevel_subject_id').val()+'&is_gaosi=1&pid='+id+'&level='+level,
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});
    }
</script>