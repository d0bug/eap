<form id="dict-edit-form" method="post" novalidate>
	<input type="hidden" name="id" value="{$dict['id']}" />
	<input type="hidden" name="grade_id" value="{$dict['grade_id']}" />
	<input type="hidden" name="subject_id" value="{$dict['subject_id']}" />
	<input type="hidden" name="category" value="{$cate}" />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt right"><span class="red">*</span> 名称：</td>
			<td>
				<input type="text" name="title" id="dict-edit-form-title" value="{$dict['title']}" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
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
		$('#dict-edit-form-title').focus();
	});
</script>