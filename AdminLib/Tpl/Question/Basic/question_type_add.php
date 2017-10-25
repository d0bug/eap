<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<div class="easyui-layout" data-options="fit: true">
		<div region="west" style="width: 250px;" data-options="collapsible: false, border: false">
			<table id="questiontype-datagrid" class="easyui-datagrid" title="题型" data-options="url: '/Question/Basic/getQuestionTypesFullBySubjectId?subjectid={$subjectid}',
																	  iconCls: 'icon-table',
																	  method: 'get',
																	  striped: true,
																	  border: true,
																	  fit: true,
																	  singleSelect: false,
																	  pagination: false,
																	  idfield: 'id',
																	  rownumbers: true,
																	  onSelect: questiontype_select,
																	  onUnselect: questiontype_unselect,
																	  rowStyler: function(index, row) {
																					 if (row.is_choose == 1){
																					 	return 'background-color:#fff;color:#000;font-weight:bold;';
																					 }
																				 }">
				<thead>
					<tr>
						<th field="origin_title" width="150">名称</th>
						<th field="is_choose" width="45" formatter="is_choose"></th>
					</tr>
				</thead>
			</table>
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div class="easyui-layout" data-options="fit: true">
				<div region="center" data-options="collapsible: false, border: false">
					<form id="questiontype-add-form" method="post" novalidate>
						<input type="hidden" name="subjectid" value="{$subjectid}" />
						<table id="questiontype-table" width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo"></table>
					</form>
				</div>
				<div region="south" style="height: 32px" data-options="collapsible: false, border: false">
					<a href="#" class="easyui-linkbutton" id="question_type_save" style="float: right" data-options="plain: true, iconCls: 'icon-ok', disabled: true" onclick="question_type_save()">保存</a>
				</div>
			</div>
		</div>
	</div>
	<script language='javascript' type='text/javascript'>
		function questiontype_select(index, row) {
			if (row.is_choose == 1) {
				return;
			}
			var code = '<input type="hidden" name="code[]" value="' + row.code + '" />';
			var alias = '<input type="text" name="title[]" value="" class="input wd_100" autocomplete="off" maxlength="100" />';
			var order = '<input type="text" name="sort[]" value="" class="input wd_25" autocomplete="off" maxlength="6" />';

			var html = '<td class="alt right wd_80">' +
								code +
								row.origin_title +
							'</td>' +
							'<td class="alt right wd_80">别名：</td>' +
							'<td>' +
								alias + 
							'</td>' +
							'<td class="alt right wd_80">排序：</td>' +
							'<td name="sort">' +
								order +
							'</td>';
			
			$('<tr id="' + row.code + '"></tr>').html(html).appendTo($('#questiontype-table'));
			$('#' + row.code).find('td[name=sort] input').numberbox({
				precision: 0,
				min: 1,
				required: true
			});
			if ($('#questiontype-table tr').length > 0) {
				$('#question_type_save').linkbutton('enable');
			}
		}
		function questiontype_unselect(index, row) {
			$('#' + row.code).remove();
			if ($('#questiontype-table tr').length == 0) {
				$('#question_type_save').linkbutton('disable');
			}
		}
		function questiontype_save() {
			$('#questiontype-add-form').form('submit', {
		        url: '/Question/Basic/question_type_add_save',
		        onSubmit: function () {
		            return $(this).form('validate');
		        },
		        success: function (result) {
		        	var result = JSON.parse(result);
		            if (result.status) {
		            	$('#basic-index-questiontype-layout').layout('remove', 'east');
		                $('#basic-index-questiontype-datagrid').datagrid('reload').datagrid('unselectAll');
		            } else {
		                $.messager.alert('错误信息', result.message, 'error');
		            }
		        }
		    });
	    }
	    function is_choose(val) {
			if (val == 1) {
				return '已添加';
			}
		}
	</script>
</body>
</html>