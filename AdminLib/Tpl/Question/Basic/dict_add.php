<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate>
			<input type="hidden" name="category" value="{$cate}" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr>
					<td class="alt right"><span class="red">*</span> 编码：</td>
					<td>
						<input type="text" name="code" value="" class="easyui-validatebox input wd_50" data-options="required: true, validType: 'remote[\'/Question/Basic/check_code?cate={$cate}\', \'code\']', invalidMessage: '编码重复'" autocomplete="off" maxlength="6" />
					</td>
				</tr>
				<tr>
					<td class="alt right"><span class="red">*</span> 名称：</td>
					<td>
						<input type="text" name="title" value="" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
					</td>
				</tr>
				<tr>
					<td class="alt right">描述：</td>
					<td>
						<textarea name="description" class="input wd_200"></textarea>
					</td>
				</tr>
				<tr>
					<td class="alt right"><span class="red">*</span> 排序：</td>
					<td>
						<input type="text" name="sort" value="" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
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
		$('#dict-add-form').form('submit', {
	        url: '/Question/Basic/dict_add_save',
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	$('#dict-add-form').form('load', {
	            		code: '',
	            		title: '',
	            		description: '',
	            		sort: ''
			        });
	                $('#basic-dict-data-datagrid').datagrid('reload');
	            } else {
	                $.messager.alert('错误信息', result.message, 'error');
	            }
	        }
	    });
    }
</script>