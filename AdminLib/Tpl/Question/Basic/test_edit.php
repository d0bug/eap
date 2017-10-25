<form id="test-edit-form" method="post" novalidate>
	<input type="hidden" name="id" value="{$dict['id']}" />
	<input type="hidden" name="grade_id" value="{$dict['grade_id']}" />
	<input type="hidden" name="city_id" value="{$dict['city_id']}" />
	<input type="hidden" name="country_id" value="{$dict['country_id']}" />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt right"><span class="red">*</span> 考区名称：</td>
			<td>
				<input type="text" name="test_name" id="test-edit-form-title" value="{$dict['test_name']}" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
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
<script language='javascript' type='text/javascript'>
	$(function() {
		$('#test-edit-form-title').focus();
	});
</script>