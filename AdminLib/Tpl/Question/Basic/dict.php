<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
	<div id="basic-dict-layout" class="easyui-layout" data-options="border: false, fit: true">
		<div region="west" style="width: 300px;" data-options="title:'类别', iconCls:'icon-table', split: true, minWidth: 300">
			<table class="easyui-datagrid" data-options="url: '/Question/Basic/getDictTypes',
																	  iconCls: 'icon-table',
																	  striped: true,
																	  border: false,
																	  fit: true,
																	  singleSelect: true,
																	  pagination: false,
																	  idfield: 'cate',
																	  rownumbers: true,
																	  onSelect: basic_dict_select">
				<thead>
					<tr>
						<th field="cate" width="150px">编码</th>
						<th field="name" width="90px">名称</th>
					</tr>
				</thead>
			</table>
		</div>
		<div region="center" data-options="title:'数据', iconCls:'icon-table'">
			<table id="basic-dict-data-datagrid" class="easyui-datagrid" data-options="
																	  method: 'get',
																	  iconCls: 'icon-table',
																	  striped: true,
																	  toolbar: '#basic-dict-data-datagrid-toolbar',
																	  border: false,
																	  fit: true,
																	  singleSelect: true,
																	  pagination: true,
																	  idField: 'id',
																	  rownumbers: true">
				<thead>
					<tr>
						<th field="code" width="80">编码</th>
						<th field="title" width="150">名称</th>
						<th field="description" width="350">描述</th>
						<th field="sort" width="50">排序</th>
					</tr>
				</thead>
			</table>
			<div id="basic-dict-data-datagrid-toolbar">
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: basic_dict_add()">添加</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: basic_dict_edit()">编辑</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: basic_dict_delete()">删除</a>
			</div>
		</div>
	</div>
	<script language='javascript' type='text/javascript'>
		var cate = '';
		function basic_dict_select(index, row) {
			cate = row.cate;
			$('#basic-dict-data-datagrid').datagrid({
				url: '/Question/Basic/getDictsByCategory?cate=' + row.cate
			}).datagrid('unselectAll');
		}
		var opts = {
            region: 'east',
            width: 350,
            collapsible: false,
            split: true,
            tools: [{
            		iconCls: 'panel-tool-close',
                  	handler: function(){
                      	$('#basic-dict-layout').layout('remove', 'east');
                    }}],
            minWidth: 350
        };
		function basic_dict_add() {
			if (cate == '')  {
				$.messager.alert('提示信息', '请选择类别!', 'info');
				return;
			}
			opts.href = '/Question/Basic/dict_add?cate=' + cate;
			opts.title = '添加';
			opts.iconCls = 'icon-add';
			open_layout(opts);
		}
		function basic_dict_edit() {
			var row = $('#basic-dict-data-datagrid').datagrid('getSelected');
			if (row) {
				opts.href = '/Question/Basic/dict_edit?id=' + row.id;
				opts.title = '编辑';
				opts.iconCls = 'icon-edit';
				open_layout(opts);
			}
			else {
				$.messager.alert('提示信息', '请选择要操作的数据!', 'info');
			}
	    }
	    function open_layout(opts) {
			$('#basic-dict-layout').layout('remove', 'east');
	        $('#basic-dict-layout').layout('add', opts);
		}
		function basic_dict_delete() {
			var row = $('#basic-dict-data-datagrid').datagrid('getSelected');
			if (row) {
				$.messager.confirm('操作提示', '您确实要删除吗？', function (r) {
					if (r) {
						$.post('/Question/Basic/dict_delete', { id: row.id }, function (result) {
							if (result.status) {
				                $('#basic-dict-data-datagrid').datagrid('reload').datagrid('unselectAll');
							} else {
								$.messager.alert('错误信息', '操作失败!', 'error');
							}
						}, 'json');
					}
				});
			} else {
				$.messager.alert('提示信息', '请选择要操作的数据!', 'info');
			}
		}
	</script>
</body>
</html>