<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-edit-form" method="post" novalidate>
			<input type="hidden" name="id" value="{$dict['id']}" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr>
					<td class="alt right wd_80"><span class="red">*</span> 编码：</td>
					<td>
						<input type="text" name="code" class="input wd_50" value="{$dict['code']}" readonly="readonly" />
					</td>
				</tr>
				<tr>
					<td class="alt right"><span class="red">*</span> 名称：</td>
					<td>
						<input type="text" name="title" value="{$dict['title']}" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
					</td>
				</tr>
				<tr>
					<td class="alt right">描述：</td>
					<td>
						<textarea name="description" class="input wd_200">{$dict['description']}</textarea>
					</td>
				</tr>
				<tr>
					<td class="alt right"><span class="red">*</span> 排序：</td>
					<td>
						<input type="text" name="sort" value="{$dict['sort']}" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div region="south" style="height: 32px; padding-top: 5px" data-options="collapsible: false, border: false">
		<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-save'" onclick="javascript: dict_save()">提交</a>
	</div>
</div>
<script language='javascript' type='text/javascript'>
	function dict_save() {
		$('#dict-edit-form').form('submit', {
	        url: '/Question/Basic/dict_edit_save',
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	$('#basic-dict-layout').layout('remove', 'east');
	                $('#basic-dict-data-datagrid').datagrid('reload').datagrid('unselectAll');
	            } else {
	                $.messager.alert('错误信息', result.message, 'error');
	            }
	        }
	    });
    }
</script>